<?php

namespace OzSpy\Models\Base;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WebCategory extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'slug', 'field', 'url'];

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
     * relationship with category
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function childCategories()
    {
        return $this->hasMany(self::class, 'web_category_id', 'id');
    }

    /**
     * relationship with category
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parentCategory()
    {
        return $this->belongsTo(self::class, 'web_category_id', 'id');
    }

    /**
     * relationship with WebProduct
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function webProducts()
    {
        return $this->belongsToMany(WebProduct::class, 'web_product_web_category', 'web_category_id', 'web_product_id')->withTimestamps();
    }
}
