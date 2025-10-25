<?php

namespace TwenyCode\LaravelBlueprint\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class BaseModel extends Model
{
    use HasFactory, LogsActivity;

    // ====================================
    // Activity Logging Configuration
    // ====================================

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }

    // ====================================
    // Casts
    // ====================================

    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'date' => 'datetime',
            'is_active' => 'boolean',
            'is_boolean' => 'boolean',
        ];
    }

    // ====================================
    // Query Scopes
    // ====================================

    // Filter active records
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Filter inactive records
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    // Order by sort_order column
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    // ====================================
    // Status Management
    // ====================================

    // Activate the record
    public function activate(): bool
    {
        return $this->update(['is_active' => true]);
    }

    // Deactivate the record
    public function deactivate(): bool
    {
        return $this->update(['is_active' => false]);
    }

    // Toggle active status
    public function toggleActive(): bool
    {
        return $this->update(['is_active' => !$this->is_active]);
    }
}