<?php

return [
    'username' => env('E_BOEKHOUDEN_USERNAME'),
    'security_code1' => env('E_BOEKHOUDEN_SECURITY_CODE1'),
    'security_code2' => env('E_BOEKHOUDEN_SECURITY_CODE2'),
    'debug' => env('E_BOEKHOUDEN_DEBUG', false),

    'invoice_template' => env('E_BOEKHOUDEN_INVOICE_TEMPLATE'),
    'invoice_payment_term' => env('E_BOEKHOUDEN_PAYMENT_TERM_DAYS', 14),
];
