<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 30/01/2018
 * Time: 8:45 PM
 */

namespace OzSpy\Http\Resources\Base;


use Illuminate\Http\Resources\Json\Resource;

abstract class ResourceContract extends Resource
{
    protected const HIDDEN_ATTRIBUTES = ['id'];

    protected function hideAttributes()
    {
        $this->resource->makeHidden(static::HIDDEN_ATTRIBUTES);
    }
}