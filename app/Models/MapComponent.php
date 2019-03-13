<?php

namespace App\Models;

use App\Filters\IndexFilter;
use App\Traits\HasCategory;
use App\Traits\HasCreatedBy;
use App\Traits\HasImage;
use App\Traits\HasRelations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class MapComponent extends Model
{
    use HasRelations, HasCategory, SoftDeletes, HasCreatedBy, HasImage, LogsActivity;

    const IMAGE_DIRECTORY_PATH = '/uploads/map-components';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'type',
        'description',
        'shape',
        'color',
        'opacity',
        'weight',
        'curved',
        'width',
        'height',
        'image',
        'category_id',
        'created_by',
        'category'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'curved' => 'boolean',
    ];

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
        'tags',
        'structures'
    ];

    /**
     * The tags that belong to the map component
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'tag_map_component');
    }

    /**
     * Get the map structures of the floor
     */
    public function structures()
    {
        return $this->hasMany(MapStructure::class);
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
        return (new IndexFilter($request))->filter($builder);
    }
}
