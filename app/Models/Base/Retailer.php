<?php

namespace OzSpy\Models\Base;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Retailer extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'abbreviation', 'domain', 'ecommerce_url', 'logo'];

    protected $dates = ['deleted_at'];

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
