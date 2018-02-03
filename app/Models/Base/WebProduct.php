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
    protected $fillable = ['retailer_product_id', 'name', 'slug', 'url', 'brand', 'model', 'sku', 'gtin8', 'gtin12', 'gtin13', 'gtin14'];

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

    /**
     * Recent Price
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function recentWebHistoricalPrice()
    {
        return $this->hasOne(WebHistoricalPrice::class, 'web_product_id', 'id')
            ->where('id', function ($subQuery) {
                $subQuery->from('web_historical_prices as recent_price_ids')
                    ->selectRaw('max(recent_price_ids.id)')
                    ->whereRaw('recent_price_ids.web_product_id=web_historical_prices.web_product_id')
                    ->groupBy('recent_price_ids.web_product_id');
            });
    }

    /**
     * Previous Price
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function previousWebHistoricalPrice()
    {
        return $this->hasOne(WebHistoricalPrice::class, 'web_product_id', 'id')
            ->where('id', function ($previousPriceSubQuery) {
                $previousPriceSubQuery->from('web_historical_prices as previous_price_ids')
                    ->selectRaw('max(previous_price_ids.id)')
                    ->whereRaw('previous_price_ids.web_product_id=web_historical_prices.web_product_id')
                    ->where('previous_price_ids.id', '!=', function ($recentPriceSubQuery) {
                        $recentPriceSubQuery->from('web_historical_prices as recent_price_ids')
                            ->selectRaw('max(recent_price_ids.id)')
                            ->whereRaw('recent_price_ids.web_product_id=web_historical_prices.web_product_id')
                            ->groupBy('recent_price_ids.web_product_id');
                    })
                    ->groupBy('previous_price_ids.web_product_id');
            });
    }
}
