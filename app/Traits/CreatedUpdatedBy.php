<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;

/**
 * Trait CreatedUpdatedBy
 * 
 * This trait automatically manages the `created_by` and `updated_by` fields for Eloquent models.
 * These fields are typically populated with Admin IDs in the system.
 *
 * Usage:
 * - Add `use CreatedUpdatedBy;` in the Eloquent model.
 * - Ensure the `created_by` and `updated_by` columns exist in the corresponding database table.
 * 
 * @property-write int|null $created_by The ID of the admin who created the model
 * @property-write int|null $updated_by The ID of the admin who last updated the model
 * @property-read Admin|null $createdBy Relationship to the admin who created the model
 * @property-read Admin|null $updatedBy Relationship to the admin who last updated the model
 * 
 * @mixin Model
 */
trait CreatedUpdatedBy
{
    /**
     * Initialize the trait
     */
    public function initializeCreatedUpdatedBy(): void
    {
        $this->fillable[] = 'created_by';
        $this->fillable[] = 'updated_by';
    }

    /**
     * Boot the CreatedUpdatedBy trait for the model.
     */
    public static function bootCreatedUpdatedBy(): void
    {
        static::creating(function (Model $model) {
            $adminId = Auth::guard('admin')->id();

            if (!isset($model->attributes['created_by'])) {
                $model->setAttribute('created_by', $adminId);
            }

            if (!isset($model->attributes['updated_by'])) {
                $model->setAttribute('updated_by', $adminId);
            }
        });

        static::updating(function (Model $model) {
            if (!isset($model->attributes['updated_by'])) {
                $model->setAttribute('updated_by', Auth::guard('admin')->id());
            }
        });
    }
}
