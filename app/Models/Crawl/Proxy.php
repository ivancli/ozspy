<?php

namespace OzSpy\Models\Crawl;

use Illuminate\Database\Eloquent\Model;

class Proxy extends Model
{
    protected $fillable = ['ip', 'port', 'is_active'];

    /**
     * is_active accessor
     * @param $value
     * @return bool
     */
    public function getIsActiveAttribute($value)
    {
        return $value === 1;
    }

    /**
     * is_active mutator
     * @param $value
     * @return void
     */
    public function setIsActiveAttribute($value)
    {
        array_set($this->attributes, 'is_active', $value === true ? 1 : 0);
    }

    /**
     * set is_active
     * @param bool $is_active
     * @return void
     */
    public function setActive($is_active = true)
    {
        $this->is_active = $is_active;
        $this->save();
    }
}
