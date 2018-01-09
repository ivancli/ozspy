<?php

namespace OzSpy\Models\Base;

use OzSpy\Models\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Retailer extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'abbreviation', 'domain', 'ecommerce_url', 'logo', 'active', 'priority', 'last_crawled_at'];

    protected $dates = ['deleted_at'];

    /**
     * Accessor - active
     * @param $value
     * @return bool
     */
    public function getActiveAttribute($value)
    {
        return $value === 1 ? true : false;
    }

    /**
     * Accessor - priority
     * @param $value
     * @return string
     */
    public function getPriorityAttribute($value)
    {
        if ($value < 4) {
            return 'low';
        } elseif ($value < 7) {
            return 'medium';
        } else {
            return 'high';
        }
    }

    /**
     * Mutator - active
     * @param $value
     * @return void
     */
    public function setActiveAttribute($value)
    {
        array_set($this->attributes, 'active', $value === true ? 1 : 0);
    }

    /**
     * Mutator - priority
     * @param $value
     * @return void
     */
    public function setPriorityAttribute($value)
    {
        if (intval($value) <= 0 || intval($value) > 10) {
            $value = 1;
        }
        array_set($this->attributes, 'priority', $value);
    }

    /**
     * relationship with WebCategory
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function webCategories()
    {
        return $this->hasMany(WebCategory::class, 'retailer_id', 'id');
    }

    /**
     * relationship with WebProduct
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function webProducts()
    {
        return $this->hasMany(WebProduct::class, 'retailer_id', 'id');
    }
}
