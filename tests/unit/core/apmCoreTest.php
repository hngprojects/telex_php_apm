<?php
namespace tests\unit\core;

use PHPUnit\Framework\TestCase;
use TelexAPM\Core\APMCore;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use TelexAPM\Events\RequestProcessed;
use TelexAPM\Events\ErrorOccurred;

class APMCoreTest extends TestCase
{
    protected $apm;

    protected function setUp(): void
    {
        parent::setUp();
        $this->apm = new APMCore();
    }

    public function testStartRequest()
    {
        $request = Request::create('/test', 'GET');
        $request->headers->set('User-Agent', 'PHPUnit');
        
        $this->apm->startRequest($request);
        
        $reflection = new \ReflectionClass($this->apm);
        $contextProperty = $reflection->getProperty('context');
        $contextProperty->setAccessible(true);
        $context = $contextProperty->getValue($this->apm);
        
        $this->assertArrayHasKey('request', $context);
        $this->assertEquals('/test', $context['request']['url']);
        $this->assertEquals('GET', $context['request']['method']);
    }

    public function testEndRequest()
    {
        Event::fake();
        
        $request = Request::create('/test', 'GET');
        $response = new Response('Test', 200);
        
        $this->apm->startRequest($request);
        $this->apm->endRequest($response);
        
        Event::assertDispatched(RequestProcessed::class);
    }

    public function testTrackError()
    {
        Event::fake();
        
        $exception = new \Exception('Test error');
        $this->apm->trackError($exception);
        
        Event::assertDispatched(ErrorOccurred::class);
    }
};