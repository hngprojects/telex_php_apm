<?php

namespace tests\unit\middleware;

use PHPUnit\Framework\TestCase;
use TelexAPM\Middleware\APMMiddleware;
use TelexAPM\services\WebhookService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class apmMiddlewareTest extends TestCase
{
    protected $middleware;
    protected $webhookService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->webhookService = $this->createMock(WebhookService::class);
        $this->middleware = new APMMiddleware($this->webhookService);
    }

    public function testHandleSuccessfulRequest()
    {
        $request = Request::create('/test', 'GET');
        $response = new Response('Test', 200);
        
        $this->webhookService
            ->expects($this->once())
            ->method('send')
            ->willReturn(true);
            
        $result = $this->middleware->handle($request, function () use ($response) {
            return $response;
        });
        
        $this->assertEquals(200, $result->getStatusCode());
    }

    public function testHandleFailedRequest()
    {
        $request = Request::create('/test', 'GET');
        $exception = new \Exception('Test error');
        
        $this->webhookService
            ->expects($this->once())
            ->method('send')
            ->willReturn(true);
            
        $this->expectException(\Exception::class);
        
        $this->middleware->handle($request, function () use ($exception) {
            throw $exception;
        });
    }
}