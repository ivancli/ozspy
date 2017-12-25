<?php

namespace OzSpy\Models\Base;

use OzSpy\Models\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WebCategory extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'slug', 'field', 'url', 'last_crawled_products_count', 'active', 'last_crawled_at'];

    protected $dates = ['deleted_at'];

    protected $hidden = [
        'last_crawled_products_count', 'active', 'last_crawled_at'
    ];

    /**
     * relationship with retailer
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function retailer()
    {
        return $this->belongsTo(Retailer::class, 'retailer_id', 'id');
    }

    /**
     * Accessor - active
     * @param $value
     * @return bool
     */
    public function getActiveAttribute($value)
    {
        return $value === 1;
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
     * recursive loading relationship with category
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function recursiveParentCategory()
    {
        return $this->parentCategory()->with('recursiveParentCategory');
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
