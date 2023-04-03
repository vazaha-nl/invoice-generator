<?php

namespace App\Services\ToggleTrack\Requests;

use App\Services\ToggleTrack\Concerns\FormatsDates;
use App\Services\ToggleTrack\Models\GroupedTimeEntry;
use App\Services\ToggleTrack\Requests\Concerns\HasWorkspaceId;
use App\Services\ToggleTrack\Responses\PagedResponse;

/**
 * @package App\Services\ToggleTrack\Requests
 * @see https://developers.track.toggl.com/docs/reports/detailed_reports
 * */
class SearchTimeEntriesRequest extends PagedRequest
{
    use HasWorkspaceId;
    use FormatsDates;

    protected string $method = 'POST';

    protected string $modelClass = GroupedTimeEntry::class;

    public function __construct(
        protected array $clientIds,
        protected string|\DateTimeInterface $startDate,
        protected string|null|\DateTimeInterface $endDate = null,
    ) {
        //
    }

    public function getEndpoint(): string
    {
        return sprintf('/reports/api/v3/workspace/%d/search/time_entries', $this->getWorkspaceId());
    }

    public function getBody(): array
    {
        return array_merge(parent::getBody(), [
            'client_ids' => $this->clientIds,
            'start_date' => $this->formatDate($this->startDate),
            'end_date' => $this->formatDate($this->endDate),
            // TODO more filters
        ]);
    }
}
