<?php

namespace App\Models;

use App\TimeEntryRenderers\ByProject;
use App\TimeEntryRenderers\Quattro;
use App\TimeEntryRenderers\Repository;
use App\TimeEntryRenderers\TimeEntryRenderer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use ValueError;

/**
 * App\Models\Client
 *
 * @property int $id
 * @property string $name
 * @property float|null $rate
 * @property int|null $toggl_id
 * @property string|null $e_boekhouden_relation_code
 * @property string|null $time_entry_renderer_class
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Project> $projects
 * @property-read int|null $projects_count
 * @method static \Illuminate\Database\Eloquent\Builder|Client newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Client newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Client query()
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereEBoekhoudenRelationCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereTimeEntryRendererClass($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereTogglId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Client extends Model
{
    use HasFactory;

    public $guarded = [];

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTimeEntryRenderer(): TimeEntryRenderer
    {
        $class = $this->time_entry_renderer_class;

        if ($class === null) {
            return (new Repository())->getDefault();
        }

        if (!is_subclass_of($class, TimeEntryRenderer::class)) {
            throw new ValueError('Invalid time entry renderer class: ' . $class);
        }

        return new $class();
    }
}
