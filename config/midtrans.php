<?php

return [

    'serverKey' => env('MIDTRANS_SERVER_KEY'),

    'clientKey' => env('MIDTRANS_CLIENT_KEY'),

    'isProduction' => filter_var(env('MIDTRANS_IS_PRODUCTION', false), FILTER_VALIDATE_BOOLEAN),

    'isSanitized' => true,

    'is3ds' => true,

];
