<?php

namespace App\Console\Commands\TogglTrack;

use App\Models\TimeEntry;
use App\Services\ToggleTrack\ApiClient;
use App\Services\ToggleTrack\Requests\DetailedReportRequest;
use Illuminate\Console\Command;

class GetTimeEntries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'toggl_track:get_time_entries {--since=} {--until=}';

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
        TimeEntry::query()->truncate();
        $request = (new DetailedReportRequest())
            ->since($this->option('since'))
            ->until($this->option('until'));

        while (true) {
            $result = $client->getReport($request);

            if (empty($result->data)) {
                break;
            }

            $this->info(sprintf('Page %d, %d entries ...', $request->getPage(), count($result->data)));

            foreach ($result->data as $entryData) {
                TimeEntry::query()->updateOrCreate(
                    [
                        'external_id' => $entryData->id,
                    ],
                    [
                        'description' => $entryData->description,
                        'projectName' => $entryData->project,
                        'clientName' => $entryData->client,
                        'start' => $entryData->start,
                        'end' => $entryData->end,
                    ]
                );
            }

            sleep(1);
            $request->nextPage();
        }
        return 0;
    }
}
