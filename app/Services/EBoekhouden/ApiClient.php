<?php

namespace App\Services\EBoekhouden;

use App\Services\EBoekhouden\Exceptions\Exception;
use App\Services\EBoekhouden\Models\Invoice;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class ApiClient
{
    protected string $sessionId;

    public function __construct(
        protected SoapClient $soapClient,
        protected string $username,
        protected string $securityCode1,
        protected string $securityCode2,
        protected ErrorHandler $errorHandler,
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

        $this->checkError($response, $methodName . 'Result', );

        return $response;
    }

    protected function parseResponse(object $response)
    {

    }

    protected function checkError(object $response, string $root)
    {
        if (property_exists($response, $root) && property_exists($response->$root, 'ErrorMsg')) {
            $error = $response->$root->ErrorMsg;

           //  throw new Exception(
           //      $error->LastErrorCode . ' ' . $error->LastErrorDescription
           // );
        }
    }

    public function addInvoice(Invoice $invoice)
    {
        return $this->doAuthenticatedCall('AddFactuur', $invoice->toArray());
    }

    public function getInvoices(
        string $invoiceNumber = null,
        string $relationCode = null,
        DateTimeInterface $from = null,
        DateTimeInterface $to = null,
    ) {
        if ($from === null) {
            $from = Carbon::parse('2020-01-01');
        }

        if ($to === null) {
            $to = Carbon::today();
        }

        return $this->doAuthenticatedCall(
            'GetFacturen',
            [
                'cFilter' => [
                    'Factuurnummer' => $invoiceNumber,
                    'Relatiecode' => $relationCode,
                    'DatumVan' => $from->format('Y-m-d'),
                    'DatumTm' => $to->format('Y-m-d'),
                ],
            ],
        );
    }

    public function getNextInvoiceNumber(): string
    {
        $result = $this->getInvoices();
        $lastInvoiceNumber = Collection::make($result->GetFacturenResult->Facturen->cFactuurList)
            ->pluck('Factuurnummer')
            ->sort()
            ->last();

        preg_match('/(\d{4})$/', $lastInvoiceNumber, $matches);
        $number = (int)$matches[1];

        $now = Carbon::now();

        // TODO make configurable?
        return sprintf(
            '%04d%02d%04d',
            $now->year,
            $now->month,
            $number +1
        );
    }

    public function getRelations(
        string $keyword = null,
        string $code = null,
        int $id = 0
    ) {
        return $this->doAuthenticatedCall(
            'GetRelaties',
            [
                'cFilter' => [
                    'Trefwoord' => $keyword,
                    'Code' => $code,
                    'ID' => $id,
                ],
            ],
        );
    }
}
