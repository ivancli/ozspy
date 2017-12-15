<?php

namespace OzSpy\Models\Base;

use OzSpy\Models\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Retailer extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'abbreviation', 'domain', 'ecommerce_url', 'logo', 'active'];

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
     * Mutator - active
     * @param $value
     * @return void
     */
    public function setActiveAttribute($value)
    {
        array_set($this->attributes, 'active', $value === true ? 1 : 0);
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
