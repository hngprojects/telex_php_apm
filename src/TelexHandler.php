<?php

namespace TelexOrg\TelexAPM;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Throwable;

class TelexHandler
{
    public function handle(\Throwable $exception)
    {
        $message = [
            'Status Code' => $exception->getCode(),
            'Url' => request()->fullUrl(),
            'Method' => request()->method(),
            'IP' => request()->ip(),
            'User Agent' => request()->userAgent(),
            'Timestamp' => date('Y-m-d H:i:s'),
            'Message' => $exception->getMessage(),
            'File' => $exception->getFile(),
            'Line' => $exception->getLine(),
            // 'Trace' => $exception->getTraceAsString(),
        ];

        $status_code = $exception->getCode();;

        $telex_msg = '';
        foreach ($message as $key => $value) {
            $telex_msg .= $key . ': ' . $value . "\n";
        }

        Log::error($telex_msg);

        if ($status_code == 404) {
            $this->notFoundExceptionNotification($telex_msg);
        }

        if ($status_code >= 500) {
            $this->internalServerExceptionNotification($telex_msg);
        }

        $this->otherUnhandleErrors($telex_msg);
    }

    public function TelexNotification($path, $event_name, $message, $status, $user_name = null){
        Http::get($path, [
            "event_name" => $event_name,
            "message" => $message,
            "status" => $status,
            "username" => $user_name,
        ]);

        return true;
    }

    public function notFoundExceptionNotification($telex_msg){
        try {
                        
            $webhookUrl = Config::get('errormonitor.404_errors');
            $app_name = Config::get('errormonitor.app_name');

            $this->TelexNotification($webhookUrl, "Page Not Found - 404", $telex_msg, "error",  $app_name);    

        } catch (Throwable $ex) {            
            Log::error($ex->getMessage());
        }
    }

    public function internalServerExceptionNotification($telex_msg){
        try {
            
            $webhookUrl = Config::get('errormonitor.500_errors');
            $app_name = Config::get('errormonitor.app_name');
        
            $this->TelexNotification($webhookUrl, "Internal Server Error - 500", $telex_msg, "error",  $app_name);    

        } catch (Throwable $ex) {            
            Log::error($ex->getMessage());
        }
    }

    public function otherUnhandleErrors($telex_msg){
        try {
            
            $webhookUrl = Config::get('errormonitor.500_errors');
            $app_name = Config::get('errormonitor.app_name');
        
            $this->TelexNotification($webhookUrl, "Unhandled Error - 500", $telex_msg, "error",  $app_name);    

        } catch (Throwable $ex) {            
            Log::error($ex->getMessage());
        }
    }
}
