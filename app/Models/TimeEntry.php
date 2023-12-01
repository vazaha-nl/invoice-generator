<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

/**
 * App\Models\TimeEntry
 *
 * @property int $id
 * @property string $description
 * @property int|null $toggl_id
 * @property int|null $project_id
 * @property \Illuminate\Support\Carbon|null $started_at
 * @property \Illuminate\Support\Carbon|null $stopped_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Client|null $client
 * @property-read \App\Models\Project|null $project
 * @method static \Illuminate\Database\Eloquent\Builder|TimeEntry newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TimeEntry newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TimeEntry query()
 * @method static \Illuminate\Database\Eloquent\Builder|TimeEntry whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeEntry whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeEntry whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeEntry whereProjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeEntry whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeEntry whereStoppedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeEntry whereTogglId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeEntry whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TimeEntry extends Model
{
    use HasFactory;

    public $guarded = [];

    protected $casts = [
        'started_at' => 'datetime',
        'stopped_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function client(): HasOneThrough
    {
        return $this->hasOneThrough(Client::class, Project::class, 'client_id', 'id');
    }

    public function getDurationInSeconds(): int
    {
        $start = $this->started_at;
        $end = $this->stopped_at ?? Carbon::now();

        return $start->diffInSeconds($end, true);
    }

    // TODO make configurable per client or something
    public function getDescription(): string
    {
        // return sprintf(
        //     '%s (%s)',
        //     $this->first()->project->name,
        //     $this->getDateString()
        // );

        return sprintf(
            '%s: %s',
            $this->project->name,
            $this->description,
            // $this->getDateString()
        );
    }
}
