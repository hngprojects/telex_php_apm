<?php

namespace TelexAPM\Services;

use TelesAPM\Contracts\WebhookInterface;
use Illuminate\Support\Facades\Http;
use TelesAPM\Exceptions\WebhookException;

class WebhookService implements WebhookInterface
{
    protected string $url;
    protected int $timeout = 5;
    
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }
    
    public function setTimeout(int $timeout): void
    {
        $this->timeout = $timeout;
    }
    
    public function send(array $data): bool
    {
        try {
            $response = Http::timeout($this->timeout)
                ->post($this->url, $data);
                
            return $response->successful();
        } catch (\Exception $e) {
            throw new WebhookException("Failed to send webhook: " . $e->getMessage());
        }
    }
}