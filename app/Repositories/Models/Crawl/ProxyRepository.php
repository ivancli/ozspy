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
     * @return \Illuminate\Database\Eloquent\Collection|Proxy[]
     */
    public function all()
    {
        return $this->proxy->all();
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
            return $this->proxy->findOrFail($id);
        } else {
            return $this->proxy->find($id);
        }
    }

    /**
     * @param $ip
     * @param $port
     * @return bool
     */
    public function exist($ip, $port)
    {
        return $this->proxy
                ->where('ip', $ip)
                ->where('port', $port)
                ->count() > 0;
    }

    /**
     * get random proxy
     * @param bool $only_active
     * @return null|Proxy
     */
    public function random($only_active = true)
    {
        if ($only_active === true) {
            return $this->proxy->where('is_active', 1)->inRandomOrder()->first();
        } else {
            return $this->proxy->inRandomOrder()->first();
        }
    }

    /**
     * create a new proxy
     * @param array $data
     * @return Proxy|null
     */
    public function store(array $data)
    {
        $data = $this->__getData($data);
        $exist = $this->exist(array_get($data, 'ip'), array_get($data, 'port'));
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
     * @return bool
     */
    public function delete(Proxy $proxy)
    {
        return $proxy->delete();
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

    /**
     * test validity of a proxy
     * @param Proxy $proxy
     * @return bool
     */
    public function test(Proxy $proxy)
    {
        $timeout = 10;

        $result = @fsockopen($proxy->ip, $proxy->port, $errCode, $errStr, $timeout);
        if ($result === false) {
            $proxy->setActive(false);
        } else {
            fclose($result);
            $proxy->setActive();
        }
    }
}