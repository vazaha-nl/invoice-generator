<?php

namespace App\Services\EBoekhouden;

use SoapClient as GlobalSoapClient;

class SoapClient extends GlobalSoapClient
{
    public function __construct(array $options)
    {
        parent::__construct('https://soap.e-boekhouden.nl/soap.asmx?wsdl', $options);
    }
}
