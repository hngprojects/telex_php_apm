<?php

namespace TelexAPM\Processors;

use TelexAPM\Events\RequestProcessed;
use TelexAPM\Events\ErrorOccurred;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebhookProcessor
{
    protected $config;
    protected $webhookUrl;

    public function __construct()
    {
        $this->config = config('apm');
        $this->webhookUrl = $this->config['webhook_url'];
    }


    public function handleRequestProcessed(RequestProcessed $event)
    {
        $payload = [
            'type' => 'request',
            'environment' => $this->config['environment'],
            'release' => $this->config['release'],
            'timestamp' => now()->toIso8601String(),
            'data' => $event->context
        ];

        $this->sendToWebhook($payload);
    }


    public function handleErrorOccurred(ErrorOccurred $event)
    {
        $payload = [
            'type' => 'error',
            'environment' => $this->config['environment'],
            'release' => $this->config['release'],
            'timestamp' => now()->toIso8601String(),
            'data' => $event->context
        ];

        $this->sendToWebhook($payload);
    }


    protected function sendToWebhook(array $payload)
    {
        try {
            $response = Http::timeout(5)
                ->retry(3, 100)
                ->post($this->webhookUrl, $payload);

            if (!$response->successful()) {
                Log::error('APM webhook failed', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('APM webhook exception', [
                'message' => $e->getMessage()
            ]);
        }
    }
}