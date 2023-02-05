<?php

namespace App\Services\EBoekhouden;

use App\Services\EBoekhouden\Exceptions\Exception;
use App\Services\EBoekhouden\Models\Invoice;
use App\Services\EBoekhouden\Requests\AddInvoiceRequest;
use App\Services\EBoekhouden\Requests\GetInvoicesRequest;
use App\Services\EBoekhouden\Requests\GetRelationsRequest;
use App\Services\EBoekhouden\Requests\Request;
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
        // TODO implement
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

    protected function doRequest(Request $request)
    {
        $response = $this->doAuthenticatedCall(
            $request->getMethodName(),
            $request->toArray(),
        );

        if (!is_null($request->getResultPath())) {
            // TODO FIXME find some less hacky way to do this
            return Arr::get(json_decode(json_encode($response), true), $request->getResultPath());
        }

        return $response;
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

    protected function checkError(object $response, string $root)
    {
        if (
            property_exists($response, $root) &&
            property_exists($response->$root, 'ErrorMsg') &&
            property_exists($response->$root->ErrorMsg, 'LastErrorDescription')
        ) {
            $error = $response->$root->ErrorMsg;

            // TODO FIXME include request payload in exception
            throw new Exception(
                $error->LastErrorDescription,
                $error->LastErrorCode
           );
        }
    }

    public function addInvoice(Invoice $invoice)
    {
        return $this->doRequest(new AddInvoiceRequest($invoice));
    }

    public function getInvoices(
        string $invoiceNumber = null,
        string $relationCode = null,
        DateTimeInterface $from = null,
        DateTimeInterface $to = null,
    ) {
        return $this->doRequest(new GetInvoicesRequest($invoiceNumber, $relationCode, $from, $to));
    }

    // TODO FIXME this is way too specific
    public function getNextInvoiceNumber(): string
    {
        $result = $this->getInvoices();
        $lastInvoiceNumber = Collection::make($result)
            ->pluck('Factuurnummer')
            ->sort()
            ->last();

        preg_match('/(\d{4})$/', $lastInvoiceNumber, $matches);
        $number = (int)$matches[1];

        $now = Carbon::now();

        return sprintf(
            '%04d%02d%04d',
            $now->year,
            $now->month,
            $number + 1
        );
    }

    public function getRelations(
        string $keyword = null,
        string $code = null,
        int $id = 0
    ) {
        return $this->doRequest(new GetRelationsRequest($keyword, $code, $id));
    }
}
