<?php

namespace OzSpy\Models\Base;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'url'];

    protected $dates = ['deleted_at'];

    /**
     * relationship with WebProduct
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function webProducts()
    {
        return $this->hasMany(WebProduct::class, 'brand_id', 'id');
    }
}
