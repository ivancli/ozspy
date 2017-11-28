<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 13/11/2017
 * Time: 9:00 PM
 */

namespace OzSpy\Repositories\Models\Crawl;


use OzSpy\Contracts\Models\Crawl\ProxyContract;
use OzSpy\Models\Crawl\Proxy;

class ProxyRepository implements ProxyContract
{
    protected $proxy;

    public function __construct(Proxy $proxy)
    {
        $this->proxy = $proxy;
    }

    /**
     * get all proxies
     * @param bool $trashed
     * @return \Illuminate\Database\Eloquent\Collection|Proxy[]
     */
    public function all($trashed = false)
    {
        if ($trashed === true) {
            return $this->proxy->withTrashed()->get();
        } else {
            return $this->proxy->all();
        }
    }

    /**
     * get proxy by id
     * @param $id
     * @param bool $throw
     * @return Proxy|null
     */
    public function get($id, $throw = true)
    {
        if ($throw === true) {
            return $this->proxy->withTrashed()->findOrFail($id);
        } else {
            return $this->proxy->withTrashed()->find($id);
        }
    }

    /**
     * @param $ip
     * @param $port
     * @param bool $trashed
     * @return bool
     */
    public function exist($ip, $port, $trashed = false)
    {
        $builder = $this->proxy;
        if ($trashed === true) {
            $builder = $builder->withTrashed();
        }
        return $builder->where('ip', $ip)
                ->where('port', $port)
                ->count() > 0;
    }

    /**
     * get random proxy
     * @param bool $trashed
     * @return null|Proxy
     */
    public function random($trashed = false)
    {
        $builder = $this->proxy;
        if ($trashed === true) {
            $builder = $builder->withTrashed();
        }
        return $builder->inRandomOrder()->first();
    }

    /**
     * create a new proxy
     * @param array $data
     * @return Proxy|null
     */
    public function store(array $data)
    {
        $data = $this->__getData($data);
        $exist = $this->exist(array_get($data, 'ip'), array_get($data, 'port'), true);
        if ($exist === false) {
            return $this->proxy->create($data);
        }
        return null;
    }

    /**
     * update an existing proxy
     * @param Proxy $proxy
     * @param array $data
     * @return bool
     */
    public function update(Proxy $proxy, array $data)
    {
        $data = $this->__getData($data);
        return $proxy->update($data);
    }

    /**
     * delete a proxy
     * @param Proxy $proxy
     * @param bool $force
     * @return bool
     */
    public function delete(Proxy $proxy, $force = false)
    {
        if ($force === true) {
            return $proxy->forceDelete();
        } else {
            return $proxy->delete();
        }
    }

    /**
     * restore a proxy
     * @param Proxy $proxy
     * @return bool
     */
    public function restore(Proxy $proxy)
    {
        return $proxy->restore();
    }

    /**
     * test validity of a proxy
     * @param Proxy $proxy
     * @return bool
     */
    public function test(Proxy $proxy)
    {
        $timeout = 2;

        $result = @fsockopen($proxy->ip, $proxy->port, $errCode, $errStr, $timeout);
        if ($result === false) {
            if (!$proxy->trashed()) {
                $this->delete($proxy);
            }
        } else {
            fclose($result);
            if ($proxy->trashed()) {
                $this->restore($proxy);
            }
        }
        return $result !== false;
    }

    /**
     * filter parameters
     * @param array $data
     * @return array
     */
    private function __getData(array $data)
    {
        return array_only($data, $this->proxy->getFillable());
    }
}