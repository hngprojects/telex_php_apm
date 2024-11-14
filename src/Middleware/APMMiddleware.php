<?php

namespace TelexAPM\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use TelexAPM\Services\WebhookService;
use Symfony\Component\HttpFoundation\Response;

class APMMiddleware
{
    protected $webhookService;
    protected $startTime;
    
    public function __construct(WebhookService $webhookService)
    {
        $this->webhookService = $webhookService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    public function handle(Request $request, Closure $next)
    {
        $this->startTime = microtime(true);

        $requestData = $this->captureRequestData($request);
        
        try {
            $response = $next($request);
            
            $this->handleSuccessfulRequest($request, $response, $requestData);
            
            return $response;
            
        } catch (\Throwable $exception) {
            $this->handleFailedRequest($request, $exception, $requestData);
            
            throw $exception;
        }
    }

    protected function captureRequestData(Request $request): array
    {
        return [
            'timestamp' => now()->toIso8601String(),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'headers' => $this->sanitizeHeaders($request->headers->all()),
            'payload' => $this->sanitizePayload($request->all()),
            'session_id' => session()->getId(),
            'user_id' => auth()->id(),
        ];
    }

    protected function handleSuccessfulRequest(Request $request, $response, array $requestData): void
    {
        $duration = $this->calculateDuration();
        $responseData = $this->captureResponseData($response);

        $data = array_merge($requestData, [
            'status' => 'success',
            'duration_ms' => $duration,
            'response' => $responseData,
        ]);

        $this->sendToWebhook($data);
    }

    protected function handleFailedRequest(Request $request, \Throwable $exception, array $requestData): void
    {
        $duration = $this->calculateDuration();

        $data = array_merge($requestData, [
            'status' => 'error',
            'duration_ms' => $duration,
            'error' => [
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $this->formatStackTrace($exception),
            ],
        ]);

        $this->sendToWebhook($data);
    }

    protected function captureResponseData($response): array
    {
        $responseData = [
            'status_code' => $response->getStatusCode(),
            'headers' => $this->sanitizeHeaders($response->headers->all()),
        ];

        // Only capture response content for certain content types
        if ($this->shouldCaptureContent($response)) {
            $responseData['content'] = $this->truncateContent($response->getContent());
        }

        return $responseData;
    }

    protected function calculateDuration(): float
    {
        return round((microtime(true) - $this->startTime) * 1000, 2); // Convert to milliseconds
    }

    protected function formatStackTrace(\Throwable $exception): array
    {
        return collect($exception->getTrace())
            ->map(function ($trace) {
                return [
                    'file' => $trace['file'] ?? null,
                    'line' => $trace['line'] ?? null,
                    'function' => $trace['function'] ?? null,
                    'class' => $trace['class'] ?? null,
                ];
            })
            ->take(20) // Limit stack trace depth
            ->toArray();
    }

    protected function sanitizeHeaders(array $headers): array
    {
        $sensitiveHeaders = ['authorization', 'cookie', 'x-xsrf-token'];
        
        return collect($headers)->map(function ($value, $key) use ($sensitiveHeaders) {
            if (in_array(strtolower($key), $sensitiveHeaders)) {
                return '[REDACTED]';
            }
            return $value;
        })->toArray();
    }

    protected function sanitizePayload(array $payload): array
    {
        $sensitiveFields = ['password', 'password_confirmation', 'credit_card', 'token'];
        
        return collect($payload)->map(function ($value, $key) use ($sensitiveFields) {
            if (in_array(strtolower($key), $sensitiveFields)) {
                return '[REDACTED]';
            }
            return $value;
        })->toArray();
    }

    protected function shouldCaptureContent($response): bool
    {
        $contentType = $response->headers->get('Content-Type');
        return str_contains($contentType, 'application/json') || 
               str_contains($contentType, 'text/html');
    }

    protected function truncateContent(string $content): string
    {
        $maxLength = config('apm.max_content_length', 1000);
        if (strlen($content) > $maxLength) {
            return substr($content, 0, $maxLength) . '...';
        }
        return $content;
    }

    protected function sendToWebhook(array $data): void
    {
        try {
            $this->webhookService->send($data);
        } catch (\Exception $e) {
            Log::error('Failed to send APM data to webhook', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
        }
    }
}