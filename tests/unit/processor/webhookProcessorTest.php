<?php
namespace tests\unit\processors;

use PHPUnit\Framework\TestCase;
use TelexAPM\Processors\WebhookProcessor;
use TelexAPM\Events\RequestProcessed;
use TelexAPM\Events\ErrorOccurred;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebhookProcessorTest extends TestCase
{
    protected $processor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->processor = new WebhookProcessor();
    }

    public function testHandleRequestProcessed()
    {
        Http::fake([
            '*' => Http::response(['status' => 'success'], 200),
        ]);
        
        $event = new RequestProcessed([
            'request' => ['url' => '/test'],
            'response' => ['status_code' => 200],
        ]);
        
        $this->processor->handleRequestProcessed($event);
        
        Http::assertSent(function ($request) {
            return $request['type'] === 'request' &&
                   $request['data']['request']['url'] === '/test';
        });
    }

    public function testHandleErrorOccurred()
    {
        Http::fake([
            '*' => Http::response(['status' => 'success'], 200),
        ]);
        
        $event = new ErrorOccurred([
            'type' => 'Exception',
            'message' => 'Test error',
        ]);
        
        $this->processor->handleErrorOccurred($event);
        
        Http::assertSent(function ($request) {
            return $request['type'] === 'error' &&
                   $request['data']['message'] === 'Test error';
        });
    }
}
