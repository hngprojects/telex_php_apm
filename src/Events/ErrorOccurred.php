<?php

namespace TelexAPM\Events;

class ErrorOccurred
{
    public $context;

    public function __construct(array $context)
    {
        $this->context = $context;
    }
}