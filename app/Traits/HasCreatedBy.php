<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

trait HasCreatedBy
{
    /**
     * Boot the global scope
     */
    protected static function bootHasCreatedBy()
    {
        static::saving(function (Model $model) {
            if (auth()->check() && empty($model->created_by)) {
                $model->created_by = auth()->user()->getKey();
            }
        });
    }

    /**
     * Get the creator
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}