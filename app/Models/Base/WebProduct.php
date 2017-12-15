<?php

namespace OzSpy\Models\Base;

use Illuminate\Database\Eloquent\SoftDeletes;
use OzSpy\Models\Model;

class WebProduct extends Model
{
    use SoftDeletes;

    protected $fillable = ['retailer_product_id', 'name', 'slug', 'url', 'brand', 'model', 'sku', 'gtin8', 'gtin12', 'gtin13', 'gtin14'];

    protected $dates = ['deleted_at'];

    public function retailer()
    {
        return $this->belongsTo(Retailer::class, 'retailer_id', 'id');
    }

    /**
     * relationship with WebCategory
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function webCategories()
    {
        return $this->belongsToMany(WebCategory::class, 'web_product_web_category', 'web_product_id', 'web_category_id')->withTimestamps();
    }

    /**
     * relationship with WebHistoricalPrice
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function webHistoricalPrices()
    {
        return $this->hasMany(WebHistoricalPrice::class, 'web_product_id', 'id');
    }

    /**
     * Recent Price
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function recentWebHistoricalPrice()
    {
        return $this->hasOne(WebHistoricalPrice::class, 'web_product_id', 'id')->orderByDesc('created_at')->limit(1);
    }

    /**
     * Previous Price
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function previousWebHistoricalPrice()
    {
        return $this->hasOne(WebHistoricalPrice::class, 'web_product_id', 'id')->orderByDesc('create_at')->offset(1)->limit(1);
    }
}
