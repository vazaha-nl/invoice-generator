<?php

namespace App\Services\EBoekhouden;

use App\Services\EBoekhouden\Models\Invoice;

class ApiClient
{
    protected string $sessionId;

    public function __construct(
        protected SoapClient $soapClient,
        protected string $username,
        protected string $securityCode1,
        protected string $securityCode2,
    ) {
        //
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
