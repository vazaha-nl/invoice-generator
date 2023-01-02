<?php

namespace App\Services\EBoekhouden;

use App\Services\EBoekhouden\Exceptions\Exception;
use App\Services\EBoekhouden\Exceptions\InvoiceNumberAlreadyExistsException;

class ErrorHandler
{
    public function handle(string $errorCode, string $errorMessage) {
        if ($errorCode === 'CFACT026') {
            // trow new InvoiceNumberAlreadyExistsException()
        }
    }
}
