<?php

return [
    'server_key' => env('MIDTRANS_SERVER_KEY'),
    'client_key' => env('MIDTRANS_CLIENT_KEY'),
    'is_sandbox' => env('MIDTRANS_IS_SANDBOX', true),
];