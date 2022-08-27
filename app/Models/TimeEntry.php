<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TimeEntry
 *
 * @property int $id
 * @property int|null $external_id
 * @property string $description
 * @property string|null $projectName
 * @property string|null $clientName
 * @property \Illuminate\Support\Carbon $started_at
 * @property \Illuminate\Support\Carbon|null $ended_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|TimeEntry newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TimeEntry newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TimeEntry query()
 * @method static \Illuminate\Database\Eloquent\Builder|TimeEntry whereClientName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeEntry whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeEntry whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeEntry whereEndedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeEntry whereExternalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeEntry whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeEntry whereProjectName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeEntry whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeEntry whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TimeEntry extends Model
{
    use HasFactory;

    public $guarded = [];

    protected $dates = [
        'started_at',
        'ended_at',
    ];

    public function getDurationInSeconds(): int
    {
        $start = $this->started_at;
        $end = $this->ended_at ?? Carbon::now();

        return $start->diffInSeconds($end, true);
    }
}
