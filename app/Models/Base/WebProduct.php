<?php

namespace OzSpy\Models\Base;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use OzSpy\Models\Model;

class WebProduct extends Model
{
    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = ['retailer_product_id', 'name', 'recent_price', 'previous_price', 'slug', 'url', 'brand', 'model', 'sku', 'gtin8', 'gtin12', 'gtin13', 'gtin14', 'price_changed_at'];

    /**
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * relationship with retailer
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
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
}
