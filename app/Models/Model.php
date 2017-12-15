<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 15/12/2017
 * Time: 9:17 PM
 */

namespace OzSpy\Models;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model as BaseModel;

abstract class Model extends BaseModel
{
    public static function createAll(array $items)
    {
        $model = with(new static);

        $now = Carbon::now();
        $items = array_map(function ($item) use ($now, $model) {
            return $model->timestamps ? array_merge([
                'created_at' => $now,
                'updated_at' => $now,
            ], $item) : $item;
        }, $items);

        return \DB::table($model->getTable())->insert($items);
    }
}