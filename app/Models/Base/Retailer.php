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
     * relationship with category
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function categories()
    {
        return $this->hasMany(WebCategory::class, 'retailer_id', 'id');
    }
}
