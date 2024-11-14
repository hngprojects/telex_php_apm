<?php

namespace TelexAPM\Contracts;

interface WebhookInterface
{
    public function send(array $data): bool;
    public function setUrl(string $url): void;
    public function setTimeout(int $timeout): void;
}