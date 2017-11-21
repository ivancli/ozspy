<?php

namespace OzSpy\Models\Base;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WebProduct extends Model
{
    use SoftDeletes;

    protected $fillable = ['retailer_product_id', 'name', 'slug', 'url', 'model', 'sku', 'gtin8', 'gtin12', 'gtin13', 'gtin14'];

    protected $dates = ['deleted_at'];

    public function retailer()
    {
        return $this->belongsTo(Retailer::class, 'retailer_id', 'id');
    }

    /**
     * relationship with Brand
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id', 'id');
    }

    /**
     * relationship with WebBrand
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function webBrands()
    {
        return $this->hasMany(WebBrand::class, 'web_product_id', 'id');
    }

    /**
     * relationship with WebCategory
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function webCategories()
    {
        return $this->belongsToMany(WebCategory::class, 'web_product_web_category', 'web_product_id', 'web_category_id')->withTimestamps();
    }
}
