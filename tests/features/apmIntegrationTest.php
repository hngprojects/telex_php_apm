<?php


namespace tests\feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use TelexAPM\Facades\APM;

class apmIntegrationTest extends TestCase
{
    public function testMiddlewareIntegration()
    {
        $response = $this->get('/test-route');
        
        // Verify response was tracked
        $this->assertTrue(
            cache()->has('apm_last_request_' . request()->path())
        );
    }

    public function testErrorTracking()
    {
        $this->withoutExceptionHandling();
        
        try {
            throw new \Exception('Test exception');
        } catch (\Exception $e) {
            APM::trackError($e);
        }
        
        // Verify error was tracked
        $this->assertTrue(
            cache()->has('apm_last_error')
        );
    }
}