<?php

namespace TwenyCode\LaravelBlueprint\Models;

use TwenyCode\LaravelBlueprint\Traits\HashingIds;
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
    use HashingIds, LogsActivity, HasFactory;

    /**
     * Configure activity logging options
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }

    /*
    |--------------------
    | MUTATORS
    |--------------------
    */

    /**
     * Set the start date attribute with proper format conversion
     */
    protected function setStartDateAttribute($value)
    {
        $this->attributes['start_date'] = $value ? DateHelper::dateTimeConversion($value, 'Y-m-d H:i:s') : null;
    }

    /**
     * Set the end date attribute with proper format conversion
     */
    protected function setEndDateAttribute($value)
    {
        $this->attributes['end_date'] = $value ? DateHelper::dateTimeConversion($value, 'Y-m-d H:i:s') : null;
    }

    /**
     * Set the generic date attribute with proper format conversion
     */
    protected function setDateAttribute($value)
    {
        $this->attributes['date'] = $value ? DateHelper::dateTimeConversion($value, 'Y-m-d H:i:s') : null;
    }


    /*
    |----------------
    | ACCESSORS
    |----------------
    */

    /**
     * Get start date formatted for display
     */
    public function getStartDateAttribute($value)
    {
        return DateHelper::dateTimeConversion($value, 'm/d/Y');
    }

    /**
     * Get end date formatted for display
     */
    public function getEndDateAttribute($value)
    {
        return DateHelper::dateTimeConversion($value, 'm/d/Y');
    }

    /**
     * Get generic date formatted for display
     */
    public function getDateAttribute($value)
    {
        return DateHelper::dateTimeConversion($value, 'm/d/Y');
    }

    /**
     * Get start date formatted for display
     */
    public function getStartAttribute()
    {
        return DateHelper::dateTimeConversion($this->start_date, 'F d, Y');
    }

    /**
     * Get created date formatted for display
     */
    public function getCreatedAttribute()
    {
        return DateHelper::dateTimeConversion($this->created_at, 'F d, Y');
    }

    /**
     * Get end date formatted for display with active status
     */
    public function getEndAttribute()
    {
        if (!is_null($this->end_date)) {
            return DateHelper::dateTimeConversion($this->end_date, 'F d, Y');
        } else {
            return '<span class="badge rounded-pill text-bg-success">Active</span>';
        }
    }

    /**
     * Get Active Status
     */
    public function getActiveAttribute()
    {
        if ( $this->isActive ?? $this->is_active) {
            return '<span class="badge rounded-pill text-bg-success">Yes</span>';
        }
        return '<span class="badge  rounded-pill text-bg-danger">No</span>';
    }



    /*
    |-----------------------
    | ADDITIONAL METHODS
    |-----------------------
    */

    /**
     * Return ID of an object based on name lookup
     *
     * @param string|null $data Name to look up
     * @return int|null The ID if found, or null
     */
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

}