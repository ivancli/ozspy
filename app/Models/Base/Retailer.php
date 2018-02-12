<?php

namespace OzSpy\Models\Base;

use OzSpy\Models\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Retailer extends Model
{
    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = ['name', 'abbreviation', 'domain', 'ecommerce_url', 'logo', 'active', 'priority', 'last_crawled_at'];

    /**
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Accessor - active
     * @param $value
     * @return bool
     */
    public function getActiveAttribute($value)
    {
        return $value === 1 || is_null($value) ? true : false;
    }

    /**
     * Accessor - priority
     * @param $value
     * @return string
     */
    public function getPriorityAttribute($value)
    {
        if ($value < 4 || is_null($value)) {
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
        array_set($this->attributes, 'active', $value === true || is_null($value) ? 1 : 0);
    }

    /**
     * Mutator - priority
     * @param $value
     * @return void
     */
    public function setPriorityAttribute($value)
    {
        $value = floatval($value);
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
