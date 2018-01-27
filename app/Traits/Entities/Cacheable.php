<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 26/01/2018
 * Time: 10:02 PM
 */

namespace OzSpy\Traits\Entities;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

trait Cacheable
{
    protected $cacheTags = [];

    /**
     * create auth user based tag
     * @return $this
     */
    protected function authBasedTag()
    {
        if (auth()->check()) {
            $user = auth()->user();
            return $this->entityBasedTag($user);
        }
        return $this;
    }

    /**
     * create new name based tag
     * @param $names
     * @return $this
     */
    protected function nameBasedTag($names)
    {
        if (is_array($names)) {
            $this->cacheTags = array_merge($this->cacheTags, $names);
        } elseif (is_string($names)) {
            $this->cacheTags[] = $names;
        }
        $this->cacheTags = array_unique($this->cacheTags);
        return $this;
    }

    /**
     * create new request based tag
     * @param Request $request
     * @param bool $unique
     * @return $this
     */
    protected function requestBasedTag(Request $request, bool $unique = true)
    {
        if ($unique === true) {
            $this->cacheTags = array_except($this->cacheTags, function ($tag) {
                return starts_with($tag, 'Request-');
            });
        }
        $params = $request->all();
        ksort_recursive($params);
        $tagName = 'Request-' . json_encode($params);
        $this->cacheTags[] = $tagName;
        return $this;
    }

    /**
     * create new entity based tag
     * @param Model $model
     * @param bool $unique
     * @return $this
     */
    protected function entityBasedTag(Model $model, bool $unique = true)
    {
        if ($unique === true && !empty($this->cacheTags)) {
            $this->cacheTags = array_except($this->cacheTags, function ($tag) use ($model) {
                return starts_with($tag, $this->__makeEntityTagName($model, false));
            });
        }
        $this->cacheTags [] = $this->__makeEntityTagName($model);

        $this->cacheTags = array_unique($this->cacheTags);

        return $this;
    }

    /**
     * @param $key
     * @return mixed
     */
    protected function get($key)
    {
        return Cache::tags($this->cacheTags)->get($key);
    }

    /**
     * @param $key
     * @param \Closure $callback
     * @return mixed
     */
    protected function remember($key, \Closure $callback)
    {
        $cacheKey = "";

        if (auth()->check()) {
            $user = auth()->user();
            $cacheKey .= get_class($user) . '-' . $user->getKey() . '-';
        }

        if (is_array($key)) {
            ksort_recursive($key);
            $cacheKey .= json_encode($key);
        } else {
            $cacheKey .= $key;
        }

        if (config('cache.enabled')) {
            return Cache::tags($this->cacheTags)->remember($cacheKey, config('cache.expiry'), $callback);
        } else {
            return $callback();
        }
    }

    /**
     * @param $key
     * @param $value
     * @return void
     */
    protected function put($key, $value)
    {
        Cache::tags($this->cacheTags)->put($key, $value, config('cache.expiry'));
    }

    /**
     * @param array $tags
     * @return void
     */
    protected function flush(array $tags = [])
    {
        if (empty($tags)) {
            $tags = $this->cacheTags;
        }
        Cache::tags($tags)->flush();
    }

    /**
     * create tag name based on model with/without key
     * @param Model $model
     * @param bool $withKey
     * @return string
     */
    private function __makeEntityTagName(Model $model, bool $withKey = true)
    {
        $tagName = get_class($model) . '-';
        if ($withKey === true) {
            $tagName .= $model->getKey();
        }
        return $tagName;
    }
}