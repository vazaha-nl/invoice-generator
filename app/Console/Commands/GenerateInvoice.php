<?php

namespace App\Console\Commands;

use App\Services\EBoekhouden\ApiClient;
use App\Services\EBoekhouden\Models\Invoice;
use App\Services\ToggleTrack\ApiClient as ToggleTrackApiClient;
use App\Services\ToggleTrack\Models\Client;
use App\Services\ToggleTrack\Models\GroupedTimeEntry;
use App\Services\ToggleTrack\Requests\GetClientsRequest;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;

class GenerateInvoice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoice:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(ApiClient $bookingClient, ToggleTrackApiClient $timeClient)
    {
        $clients = $timeClient->getClients()->getModels();
        $clientNames = $clients->map(fn (Client $client) => $client->getName())->toArray();

        $clientName = $this->choice('Select the client from your time tracker:', $clientNames);

        /** @var Client $client */
        $client = $clients->first(fn (Client $client) => $client->getName() === $clientName);

        $clientModel = $client->toEloquentModel();

        if ($clientModel->e_boekhouden_relation_code === null) {

            $relations = $bookingClient->getRelations();
            $relationCodes = Collection::make($relations)->pluck('Code')->toArray();
            $relationCode = $this->choice('Select the corresponding booking relation you want to generate an invoice for:', $relationCodes);


            if ($clientModel->e_boekhouden_relation_code !== null && $clientModel->e_boekhouden_relation_code !== $relationCode) {
                dd('time client is different from booking relation code, check your db, TODO FIX with better error');
            }

            if ($clientModel->e_boekhouden_relation_code === null) {
                if ($this->confirm(sprintf('Is the time tracking client "%s" the same as the booking relation "%s"?', $clientName, $relationCode), true)) {
                    $clientModel->update(['e_boekhouden_relation_code' => $relationCode]);
                }
            }
        }

        $defaultSince = Carbon::parse('first day of previous month')->format('Y-m-d');
        $since = $this->ask('Enter the start date of the invoice period:', $defaultSince);

        $defaultUntil = Carbon::parse('last day of previous month')->format('Y-m-d');
        $until = $this->ask('Enter the end date of the invoice period (inclusive!):', $defaultUntil);

        $timeEntries = $this->getTimeEntries($timeClient, $client, $since, $until);

        $this->info('Generating invoice ...');

        $invoiceNumber = $bookingClient->getNextInvoiceNumber();
        $invoice = (new Invoice())
            ->setNumber($invoiceNumber)
            ->setRelationCode($clientModel->e_boekhouden_relation_code);
        $invoice->generateLines($timeEntries);

        $response = $bookingClient->addInvoice($invoice);

        // dump($invoice->toArray());

        $this->info('Done, invoice created with number ' . $invoiceNumber);

        return 0;
    }

    protected function getTimeEntries(ToggleTrackApiClient $apiClient, Client $client, $since, $until): Collection
    {
        $this->info('Getting time entries from time tracking api ...');
        $timeEntries = new Collection();

        $timeEntriesResponse = $apiClient->searchTimeEntries([$client->getId()], $since, $until);
        $timeEntries = $timeEntriesResponse->getModels();

        while ($timeEntriesResponse->hasNextPage()) {
            $timeEntriesResponse = $timeEntriesResponse->getNextPage();
            $timeEntries->concat($timeEntriesResponse->getModels());
        }

        $this->info('Committing to db ...');

        return $timeEntries->map(fn (GroupedTimeEntry $entry) => $entry->toEloquentModel());
    }

}
