<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

/**
 * Trait CreatedUpdatedBy
 * 
 * This trait automatically manages the `created_by` and `updated_by` fields for Eloquent models.
 * - `created_by`: Set when the model is created.
 * - `updated_by`: Set when the model is created or updated.
 *
 * Usage:
 * - Add `use CreatedUpdatedBy;` in the Eloquent model.
 * - Ensure the `created_by` and `updated_by` columns exist in the corresponding database table.
 */
trait CreatedUpdatedBy
{
    /**
     * Boot the CreatedUpdatedBy trait for the model.
     * 
     * This method hooks into the model's lifecycle events to automatically
     * set the `created_by` and `updated_by` attributes based on the authenticated user.
     */
    public static function bootCreatedUpdatedBy()
    {
        // Set `created_by` and `updated_by` during model creation.
        static::creating(function ($model) {
            $userId = Auth::id();

            if (! $model->isDirty('created_by')) {
                $model->created_by = $userId;
            }

            if (! $model->isDirty('updated_by')) {
                $model->updated_by = $userId;
            }
        });

        // Update `updated_by` during model updates.
        static::updating(function ($model) {
            if (! $model->isDirty('updated_by')) {
                $model->updated_by = Auth::id();
            }
        });
    }
}
