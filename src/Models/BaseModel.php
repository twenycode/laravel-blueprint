<?php

namespace TwenyCode\LaravelCore\Models;

use TwenyCode\LaravelCore\Traits\HashingIds;
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
        $this->attributes['start_date'] = $value ? \YourCompany\LaravelCore\Helpers\DateHelper::dateTimeConversion($value, 'Y-m-d H:i:s') : null;
    }

    /**
     * Set the end date attribute with proper format conversion
     */
    protected function setEndDateAttribute($value)
    {
        $this->attributes['end_date'] = $value ? \YourCompany\LaravelCore\Helpers\DateHelper::dateTimeConversion($value, 'Y-m-d H:i:s') : null;
    }

    /**
     * Set the date of birth attribute with proper format conversion
     */
    protected function setDobAttribute($value)
    {
        $this->attributes['dob'] = $value ? \YourCompany\LaravelCore\Helpers\DateHelper::dateTimeConversion($value, 'Y-m-d H:i:s') : null;
    }

    /**
     * Set the date of joining attribute with proper format conversion
     */
    protected function setDojAttribute($value)
    {
        $this->attributes['doj'] = $value ? \YourCompany\LaravelCore\Helpers\DateHelper::dateTimeConversion($value, 'Y-m-d H:i:s') : null;
    }

    /**
     * Set the generic date attribute with proper format conversion
     */
    protected function setDateAttribute($value)
    {
        $this->attributes['date'] = $value ? \YourCompany\LaravelCore\Helpers\DateHelper::dateTimeConversion($value, 'Y-m-d H:i:s') : null;
    }

    /**
     * Clean up descriptions by trimming whitespace
     */
    protected function setDescriptionsAttribute($value)
    {
        $this->attributes['descriptions'] = trim($value);
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
        return \YourCompany\LaravelCore\Helpers\DateHelper::dateTimeConversion($value, 'm/d/Y');
    }

    /**
     * Get end date formatted for display
     */
    public function getEndDateAttribute($value)
    {
        return \YourCompany\LaravelCore\Helpers\DateHelper::dateTimeConversion($value, 'm/d/Y');
    }

    /**
     * Get date of birth formatted for display
     */
    public function getDobAttribute($value)
    {
        return \YourCompany\LaravelCore\Helpers\DateHelper::dateTimeConversion($value, 'm/d/Y');
    }

    /**
     * Get date of joining formatted for display
     */
    public function getDojAttribute($value)
    {
        return \YourCompany\LaravelCore\Helpers\DateHelper::dateTimeConversion($value, 'm/d/Y');
    }

    /**
     * Get generic date formatted for display
     */
    public function getDateAttribute($value)
    {
        return \YourCompany\LaravelCore\Helpers\DateHelper::dateTimeConversion($value, 'm/d/Y');
    }

    /**
     * Get active status with HTML formatting
     */
    public function getActiveAttribute()
    {
        $isActive = $this->is_active ?? $this->isActive ?? false;
        if ($isActive) {
            return '<span class="badge rounded-pill text-bg-success">Yes</span>';
        }
        return '<span class="badge rounded-pill text-bg-danger">No</span>';
    }

    /**
     * Get start date formatted for display
     */
    public function getStartAttribute()
    {
        return \YourCompany\LaravelCore\Helpers\DateHelper::dateTimeConversion($this->start_date, 'F d, Y');
    }

    /**
     * Get created date formatted for display
     */
    public function getCreatedAttribute()
    {
        return \YourCompany\LaravelCore\Helpers\DateHelper::dateTimeConversion($this->created_at, 'F d, Y');
    }

    /**
     * Get end date formatted for display with active status
     */
    public function getEndAttribute()
    {
        if (!is_null($this->end_date)) {
            return \YourCompany\LaravelCore\Helpers\DateHelper::dateTimeConversion($this->end_date, 'F d, Y');
        } else {
            return '<span class="badge rounded-pill text-bg-success">Active</span>';
        }
    }

    /**
     * Get birth date formatted for display
     */
    public function getBirthDateAttribute()
    {
        return \YourCompany\LaravelCore\Helpers\DateHelper::dateTimeConversion($this->dob, 'd M, Y');
    }

    /**
     * Get join date formatted for display
     */
    public function getJoinDateAttribute()
    {
        return \YourCompany\LaravelCore\Helpers\DateHelper::dateTimeConversion($this->doj, 'd M, Y');
    }

    /**
     * Get full name including acronym if available
     */
    public function getFullNameAttribute()
    {
        if (isset($this->acronym) && !empty($this->acronym)) {
            return $this->name . ' (' . $this->acronym . ')';
        }
        return $this->name;
    }

    /**
     * Get full address
     */
    public function getFullAddressAttribute()
    {
        $address = $this->home_address ?? $this->physical_address ?? null;
        if ($address) {
            return $this->name . ' of ' . $address;
        }
        return $this->name;
    }

    /**
     * Format URL as HTML link
     */
    public function getLinkAttribute()
    {
        if (!is_null($this->url)) {
            return '<a class="badge rounded-pill text-bg-primary" href="' . $this->url . '" target="_blank" style="text-decoration: none;">Click</a>';
        }
        return '<span class="badge rounded-pill text-bg-danger" style="text-decoration: none;">insert</span>';
    }

    /*
    |--------------------
    | RELATIONSHIPS
    |--------------------
    */

    /**
     * Define common relationships that can be inherited by child models
     * These can be overridden or extended in child classes
     */

    /**
     * Generic relationship to Country model
     */
    public function country()
    {
        return $this->belongsTo(config('core.models.country', \App\Models\Country::class))->withDefault();
    }

    /**
     * Generic relationship to Address model
     */
    public function address()
    {
        return $this->belongsTo(config('core.models.address', \App\Models\Address::class))->withDefault();
    }

    /**
     * Generic relationship to Employee model
     */
    public function employee()
    {
        return $this->belongsTo(config('core.models.employee', \App\Models\Employee::class))->withDefault();
    }

    /**
     * Generic relationship to Department model
     */
    public function department()
    {
        return $this->belongsTo(config('core.models.department', \App\Models\Department::class))->withDefault();
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

    /**
     * Return address ID of an object based on name lookup
     *
     * @param string|null $data Name to look up
     * @return object|null The object with ID and address if found, or null
     */
    public static function returnAddressId($data)
    {
        if (!is_null($data)) {
            $objectId = self::select('id', 'address')->where('name', $data)->first();
            if (!is_null($objectId)) {
                return $objectId;
            }
        }
        return null;
    }

    /*
    * Local Scopes
    */

    /**
     * Scope for active records
     */
    public function scopeIsActive($query)
    {
        return $query->where('isActive', true);
    }

    /**
     * Scope to select address and name columns
     */
    public function scopeAddressName($query)
    {
        return $query->select('id', 'name', 'address');
    }

    /**
     * Scope to order by updated_at descending
     */
    public function scopeOrderByUpdated($query)
    {
        return $query->orderBy('updated_at', 'desc');
    }

    /**
     * Scope to select only id and name columns
     */
    public function scopeSelectNameId($query)
    {
        return $query->select('id', 'name');
    }

    /**
     * Scope to order by name
     */
    public function scopeOrderByName($query, $direction = 'asc')
    {
        return $query->orderBy('name', $direction);
    }

    /**
     * Scope to search by name
     */
    public function scopeSearchName($query, $value)
    {
        return $query->where('name', 'like', '%' . $value . '%');
    }

    /**
     * Scope to search by address
     */
    public function scopeSearchAddress($query, $value)
    {
        return $query->orWhere('name', 'like', '%' . $value . '%');
    }
}