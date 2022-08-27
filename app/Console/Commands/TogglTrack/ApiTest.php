<?php

namespace App\Console\Commands\TogglTrack;

use App\Services\ToggleTrack\ApiClient;
use App\Services\ToggleTrack\Requests\DetailedReportRequest;
use Illuminate\Console\Command;

class ApiTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'toggl_track:api_test';

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
        $response = $client->getReport(new DetailedReportRequest());
        dump($response);
        return 0;
    }
}
