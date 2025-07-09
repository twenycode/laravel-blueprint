<?php

namespace TwenyCode\LaravelBlueprint\Models;

use TwenyCode\LaravelBlueprint\Traits\UuidTrait;
use TwenyCode\LaravelBlueprint\Helpers\DateHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;


/**
 * Base Model class with common functionality for all models
 */
class BaseModel extends Model
{
    use UuidTrait, LogsActivity, HasFactory;

    /**
     * Configure activity logging options
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }



    /**
     * Mutators Variables
     * -----------------------
     */

    //  Set the start date attribute with proper format conversion
    protected function setStartDateAttribute($value)
    {
        $this->attributes['start_date'] = $value ? DateHelper::dateTimeConversion($value, 'Y-m-d H:i:s') : null;
    }

    //  Set the end date attribute with proper format conversion
    protected function setEndDateAttribute($value)
    {
        $this->attributes['end_date'] = $value ? DateHelper::dateTimeConversion($value, 'Y-m-d H:i:s') : null;
    }

    //  Set the generic date attribute with proper format conversion
    protected function setDateAttribute($value)
    {
        $this->attributes['date'] = $value ? DateHelper::dateTimeConversion($value, 'Y-m-d H:i:s') : null;
    }


    /**
     * Accessors Variables
     * -----------------------
     */

    //  Get start date formatted for display
    public function getStartDateAttribute($value)
    {
        return DateHelper::dateTimeConversion($value, 'm/d/Y');
    }

    //  Get end date formatted for display
    public function getEndDateAttribute($value)
    {
        return DateHelper::dateTimeConversion($value, 'm/d/Y');
    }

    //  Get generic date formatted for display
    public function getDateAttribute($value)
    {
        return DateHelper::dateTimeConversion($value, 'm/d/Y');
    }

    //  Get start date formatted for display
    public function getStartAttribute()
    {
        return DateHelper::dateTimeConversion($this->start_date, 'F d, Y');
    }

    //  Get created date formatted for display
    public function getCreatedAttribute()
    {
        return DateHelper::dateTimeConversion($this->created_at, 'F d, Y');
    }

    //  Get end date formatted for display with active status
    public function getEndAttribute()
    {
        if (!is_null($this->end_date)) {
            return DateHelper::dateTimeConversion($this->end_date, 'F d, Y');
        } else {
            return '<span class="badge rounded-pill text-bg-success">Active</span>';
        }
    }

    //  Get Active Status
    public function getActiveAttribute()
    {
        if ( $this->is_active) {
            return '<span class="badge text-bg-success p-2">Yes</span>';
        }
        return '<span class="badge text-bg-danger p-2">No</span>';
    }


    /**
     * Local Scopes
     * ----------------
     */
    //  Scope to get only active categories
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    //  Scope to get only inactive categories
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    //  Scope to order categories by sort_order
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    //  Scope to get only boolean features (yes/no features)
    public function scopeBoolean($query)
    {
        return $query->where('is_boolean', true);
    }


    /**
     * Additional Methods
     * -----------------------
     */

    //  Return ID of an object based on name lookup
    public static function returnID($data)
    {
        if (!is_null($data)) {
            $objectId = self::select('id')->where('name', $data)->first();
            if (!is_null($objectId)) {
                return $objectId->id;
            }
        }
        return null;
    }

    // Simple status toggles
    public function activate()
    {
        return self::update(['is_active' => true]);
    }

    public function deactivate()
    {
        return self::update(['is_active' => false]);
    }


}