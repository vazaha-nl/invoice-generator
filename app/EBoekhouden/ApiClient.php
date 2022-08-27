<?php

namespace App\EBoekhouden;

use App\EBoekhouden\Models\Invoice;
use SoapClient;

class ApiClient
{
    protected SoapClient $soapClient;

    protected string $username;

    protected string $securityCode1;

    protected string $securityCode2;

    protected string $sessionId;

    public function __construct(array $options = [])
    {
        $this->username = config('eboekhouden.username');
        $this->securityCode1 = config('eboekhouden.security_code1');
        $this->securityCode2 = config('eboekhouden.security_code2');
        $this->soapClient = new SoapClient('https://soap.e-boekhouden.nl/soap.asmx?wsdl', $options);
    }

    public function checkSession(): void
    {
        if (isset($this->sessionId)) {
            return;
        }

        $result = $this->soapClient->__soapCall('OpenSession', [
            'input' => [
                'Username' => $this->username,
                'SecurityCode1' => $this->securityCode1,
                'SecurityCode2' => $this->securityCode2,
            ],
        ]);

        $this->sessionId = $result->OpenSessionResult->SessionID;
    }

    public function closeSession(): void
    {
        if (!isset($this->sessionId)) {
            return;
        }

        $this->soapClient->__soapCall('CloseSession', [
            'input' => [
                'SessionID' => $this->sessionId,
            ],
        ]);
        unset($this->sessionId);
    }

    protected function doAuthenticatedCall(string $methodName, array $input)
    {
        $this->checkSession();

        $input = array_merge($input, [
            'SessionID' => $this->sessionId,
            'SecurityCode2' => $this->securityCode2,
        ]);

        $response = $this->soapClient->__soapCall($methodName, ['input' => $input]);

        return $response;
    }

    public function addInvoice(Invoice $invoice)
    {
        return $this->doAuthenticatedCall('AddFactuur', $invoice->toArray());
    }
}
