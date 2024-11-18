<?php

namespace TelexOrg\TelexAPM\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

class MetricsService
{
    public function collectMetrics($request, $response, $duration)
    {
        $statusCode = $response->getStatusCode();
        
        $metrics = [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'status_code' => $statusCode,
            'duration' => $duration,
            'timestamp' => date('Y-m-d H:i:s'),
        ];

        // Determine the webhook URL based on the status code
        $webhookUrl = match ($statusCode) {
            404 => Config::get('myapm.404_webhook_url'),
            500 => Config::get('myapm.500_webhook_url'),
            default => Config::get('myapm.webhook_url'),
        };

        // Send metrics to the configured webhook
        if ($webhookUrl) {
            Http::post($webhookUrl, $metrics);
        }
    }
}
