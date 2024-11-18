<?php

namespace TelexOrg\TelexAPM\Exception;

use Illuminate\Foundation\Exceptions\Handler as BaseHandler;
use Illuminate\Support\Facades\App;
use Throwable;
use TelexOrg\TelexAPM\Services\MetricsService;

class ExceptionHandler extends BaseHandler
{
    protected $metricsService;

    public function __construct()
    {
        parent::__construct(App::make('Illuminate\Contracts\Container\Container'));
        
        // Initialize MetricsService
        $this->metricsService = App::make(MetricsService::class);
    }

    public function report(Throwable $exception)
    {
        if ($this->shouldReport($exception) && method_exists($exception, 'getStatusCode')) {
            $statusCode = $exception->getStatusCode();

            // Capture 404 and 500 errors
            if ($statusCode === 404 || $statusCode === 500) {
                $this->metricsService->collectMetrics(request(), response(), 0);
            }
        }

        parent::report($exception);
    }
}
