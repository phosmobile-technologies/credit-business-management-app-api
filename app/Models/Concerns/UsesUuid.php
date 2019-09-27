<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

/**
 * This trait adds UUID creation functionality to a model
 *
 * @package App\Models\Concerns
 */
trait UsesUuid
{
    protected static function bootUsesUuid()
    {
        static::creating(function ($model) {
            if (! $model->getKey()) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    /**
     * Determines if the id field on a table is incrementing.
     *
     * @return bool
     */
    public function getIncrementing()
    {
        return false;
    }

    /**
     * Specifies that the ID  on a table should be stored as a string.
     * @return string
     */
    public function getKeyType()
    {
        return 'string';
    }
}
