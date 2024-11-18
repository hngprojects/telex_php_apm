<?php

return [
    'webhook_url' => env("DEFAULT_WEBHOOK_URL", null),
    '404_errors' => env("NOT_FOUND_URL_404", null),
    '500_errors' => env("INTERNAL_SERVER_ERROR_URL_500", null),
    'app_name' => env("TELEX_APP_NAME", null),
];
