<?php

namespace TelexAPM\Events;

class RequestProcessed
{
    public $context;

    public function __construct(array $context)
    {
        $this->context = $context;
    }
}