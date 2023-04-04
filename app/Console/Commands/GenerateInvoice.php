<?php

namespace App\Console\Commands;

use App\Models\Client as EloquentClient;
use App\Services\EBoekhouden\ApiClient as BookingApiClient;
use App\Services\EBoekhouden\Models\Invoice;
use App\Services\ToggleTrack\ApiClient as TimeTrackingApiClient;
use App\Services\ToggleTrack\Models\Client;
use App\Services\ToggleTrack\Models\GroupedTimeEntry;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

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
    protected $description = 'Interactively generate and create an invoice based on time tracking data';

    protected BookingApiClient $bookingApiClient;

    protected TimeTrackingApiClient $timeTrackingApiClient;

    protected function selectTimeTrackingClient(): Client
    {
        $clients = $this->timeTrackingApiClient->getClients()->getModels();
        $clientNames = $clients->map(fn (Client $client) => $client->getName())->toArray();
        $clientName = $this->choice('Select the client from your time tracker:', $clientNames);

        return $clients->first(fn (Client $client) => $client->getName() === $clientName);
    }

    protected function matchTimeTrackingClientWithBookingRelation(Client $client): EloquentClient
    {
        $eloquentClient = $client->toEloquentModel();

        if ($eloquentClient->e_boekhouden_relation_code !== null) {
            $alreadyMatched = $this->confirm(
                sprintf('Does time tracking client "%s" match with booking relation "%s"?', $client->getName(), $eloquentClient->e_boekhouden_relation_code),
                true
            );

            if ($alreadyMatched) {
                return $eloquentClient;
            }
        }

        $relations = $this->bookingApiClient->getRelations();
        $relationCodes = Collection::make($relations)->pluck('Code')->toArray();
        $relationCode = $this->choice('Select the corresponding booking relation you want to generate an invoice for:', $relationCodes);

        $matchConfirmed = $this->confirm(
            sprintf('Is the time tracking client "%s" the same as the booking relation "%s"?', $eloquentClient->getName(), $relationCode),
            true
        );

        if ($matchConfirmed) {
            $eloquentClient->update(['e_boekhouden_relation_code' => $relationCode]);

            return $eloquentClient;
        }

        // try again
        return $this->matchTimeTrackingClientWithBookingRelation($client);
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(BookingApiClient $bookingApiClient, TimeTrackingApiClient $timeKeepingApiClient)
    {
        $this->bookingApiClient = $bookingApiClient;
        $this->timeTrackingApiClient = $timeKeepingApiClient;

        $client = $this->selectTimeTrackingClient();
        $eloquentClient = $this->matchTimeTrackingClientWithBookingRelation($client);

        $defaultSince = Carbon::parse('first day of previous month')->format('Y-m-d');
        $since = $this->ask('Enter the start date of the invoice period:', $defaultSince);

        $defaultUntil = Carbon::parse('last day of previous month')->format('Y-m-d');
        $until = $this->ask('Enter the end date of the invoice period (inclusive!):', $defaultUntil);

        $timeEntries = $this->getTimeEntries($client, $since, $until);

        $this->info('Generating invoice ...');

        $invoiceNumber = $bookingApiClient->getNextInvoiceNumber();
        $invoice = (new Invoice())
            ->setNumber($invoiceNumber)
            ->setRelationCode($eloquentClient->e_boekhouden_relation_code);
        $invoice->generateLines($timeEntries);

        $bookingApiClient->addInvoice($invoice);

        $this->info('Done, invoice created with number ' . $invoiceNumber);

        return 0;
    }

    protected function getTimeEntries(Client $client, $since, $until): Collection
    {
        $this->info('Getting time entries from time tracking api ...');
        $timeEntries = new Collection();

        $timeEntriesResponse = $this->timeTrackingApiClient->searchTimeEntries([$client->getId()], $since, $until);
        $timeEntries = $timeEntriesResponse->getModels();

        while ($timeEntriesResponse->hasNextPage()) {
            $timeEntriesResponse = $timeEntriesResponse->getNextPage();
            $timeEntries->concat($timeEntriesResponse->getModels());
        }

        $this->info('Committing to db ...');

        return $timeEntries->map(fn (GroupedTimeEntry $entry) => $entry->toEloquentModel());
    }

}
