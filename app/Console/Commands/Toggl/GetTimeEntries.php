<?php

namespace App\Console\Commands\Toggl;

use App\Models\TimeEntry;
use App\ToggleTrack\ApiClient;
use Illuminate\Console\Command;

class GetTimeEntries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'toggl:get_time_entries';

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
        $page = 1;

        TimeEntry::query()->truncate();

        while (true) {
            $result = $client->getDetailedReport([
                'page' => $page,
                'since' => '2022-07-01',
                'until' => '2022-07-31',
            ]);

            if (empty($result->data)) {
                break;
            }

            $this->info(sprintf('Page %d, %d entries ...', $page, count($result->data)));

            foreach ($result->data as $entryData) {
                TimeEntry::query()->updateOrCreate(
                    [
                        'external_id' => $entryData->id,
                    ],
                    [
                        'description' => $entryData->description,
                        'project' => $entryData->project,
                        'client' => $entryData->client,
                        'start' => $entryData->start,
                        'end' => $entryData->end,
                    ]
                );
            }

            sleep(1);

            $page++;
        }
        return 0;
    }
}
