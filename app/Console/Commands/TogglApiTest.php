<?php

namespace App\Console\Commands;

use App\Services\ToggleTrack\ApiClient;
use Illuminate\Console\Command;

class TogglApiTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'toggl:api_test';

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
        $response = $client->getDetailedReport();
        dump($response);
        return 0;
    }
}
