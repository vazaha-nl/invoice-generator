<?php

namespace App\Console\Commands;

use App\Services\EBoekhouden\ApiClient;
use App\Services\EBoekhouden\Models\Invoice;
use App\Services\ToggleTrack\ApiClient as ToggleTrackApiClient;
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
        $invoice = new Invoice();

        $relations = $bookingClient->getRelations();
        $relationCodes = Collection::make($relations)->pluck('Code')->toArray();
        $relationCode = $this->choice('Select the booking relation you want to generate an invoice for:', $relationCodes);

        $clients = $timeClient->doRequest(new GetClientsRequest());
        $clientNames = Collection::make($clients)->pluck('name')->toArray();

        $clientName = $this->choice('Select the corresponding client from your time tracker:', $clientNames);

        $defaultSince = Carbon::parse('first day of previous month')->format('Y-m-d');
        $since = $this->ask('Enter the start date of the invoice period:', $defaultSince);

        $defaultUntil = Carbon::parse('last day of previous month')->format('Y-m-d');
        $until = $this->ask('Enter the end date of the invoice period (inclusive!):', $defaultUntil);

        $invoice = (new Invoice())
            ->setRelationCode($relationCode);

        Artisan::call('toggl_track:get_time_entries', [
            '--since' => $since,
            '--until' => $until,
        ]);

        return 0;
    }
}
