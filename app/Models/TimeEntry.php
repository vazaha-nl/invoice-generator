<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TimeEntry
 *
 * @property int $id
 * @property string $description
 * @property string $project
 * @property string $client
 * @property \Illuminate\Support\Carbon $start
 * @property \Illuminate\Support\Carbon|null $end
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|TimeEntry newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TimeEntry newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TimeEntry query()
 * @method static \Illuminate\Database\Eloquent\Builder|TimeEntry whereClient($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeEntry whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeEntry whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeEntry whereEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeEntry whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeEntry whereProject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeEntry whereStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeEntry whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TimeEntry extends Model
{
    use HasFactory;

    public $guarded = [];

    protected $dates = [
        'start',
        'end',
    ];

    public function getDurationInSeconds(): int
    {
        $start = $this->start;
        $end = $this->end ?? Carbon::now();

        return $start->diffInSeconds($end, true);
    }
}
