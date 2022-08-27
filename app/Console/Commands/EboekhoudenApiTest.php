<?php

namespace App\Console\Commands;

use App\Services\EBoekhouden\ApiClient;
use App\Services\EBoekhouden\Models\Invoice;
use App\Models\TimeEntry;
use Illuminate\Console\Command;

class EboekhoudenApiTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'eboekhouden:api_test';

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
    public function handle(ApiClient $client)
    {
        // $client->checkSession();
        // $client->closeSession();

        $invoice = new Invoice();
        $invoice->generateLines(TimeEntry::all());
        dump($invoice->toArray());
        $result = $client->addInvoice($invoice);
        dump($result);
        return 0;
    }
}
