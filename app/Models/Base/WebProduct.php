<?php

namespace OzSpy\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use OzSpy\Models\Model;

class WebProduct extends Model
{
    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = ['retailer_product_id', 'name', 'recent_price', 'previous_price', 'slug', 'url', 'brand', 'model', 'sku', 'gtin8', 'gtin12', 'gtin13', 'gtin14', 'last_scraped_at', 'price_changed_at'];

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

    /**
     * @param $value
     * @return Carbon|null
     */
    public function getLastScrapedAtAttribute($value)
    {
        if (!is_null($value)) {
            return Carbon::parse($value);
        }
        return $value;
    }

    /**
     * @param $value
     * @return Carbon|null
     */
    public function getPriceChangedAtAttribute($value)
    {
        if (!is_null($value)) {
            return Carbon::parse($value);
        }
        return $value;
    }

    /**
     * @param Builder $builder
     * @return Builder|static
     */
    public function scopeHasRecentPrice(Builder $builder)
    {
        return $builder->hasAttribute('recent_price');
    }

    /**
     * @param Builder $builder
     * @return Builder|static
     */
    public function scopeHasPreviousPrice(Builder $builder)
    {
        return $builder->hasAttribute('previous_price');
    }

    /**
     * @param Builder $builder
     * @return Builder|static
     */
    public function scopeHasPriceDrop(Builder $builder)
    {
        return $builder->hasRecentPrice()->hasPreviousPrice()->where('recent_price', '<', 'previous_price');
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function scopeHasPriceRaise(Builder $builder)
    {
        return $builder->hasRecentPrice()->hasPreviousPrice()->where('recent_price', '>', 'previous_price');
    }
}
