<?php

namespace App\Models;

use App\Filters\BeaconFilter;
use App\Pivots\BeaconContainer;
use App\Traits\HasCategory;
use App\Traits\HasCreatedBy;
use App\Traits\HasRelations;
use Askedio\SoftCascade\Traits\SoftCascadeTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Beacon extends Model
{
    use HasRelations, HasCategory, SoftDeletes, HasCreatedBy, LogsActivity, SoftCascadeTrait;

    /**
     * @var array
     */
    protected $softCascade = [
        'locations'
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'deleted_at'
    ];

    /**
     * Model Relations
     *
     * @var array
     */
    public $relationships = [
        'category',
        'tags',
        'containers',
        'locations'
    ];

    /**
     * Get the locations for the Poi.
     */
    public function locations()
    {
        return $this->morphMany(Location::class, 'locatable');
    }

    /**
     * The tags that belong to the beacon
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'tag_beacon');
    }

    /**
     * The content containers that belong to the user.
     */
    public function containers()
    {
        return $this->belongsToMany(Container::class)
            ->using(BeaconContainer::class)->withPivot(['id']);
    }

    /**
     * Process filters
     *
     * @param Builder $builder
     * @param $request
     *
     * @return Builder $builder
     */
    public function scopeFilter(Builder $builder, $request)
    {
        return (new BeaconFilter($request))->filter($builder);
    }
}
