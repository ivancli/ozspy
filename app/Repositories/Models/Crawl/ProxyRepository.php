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
     * get random proxy
     * @return Proxy|null
     */
    public function random()
    {
        return $this->proxy->inRandomOrder()->first();
    }

    /**
     * create a new proxy
     * @param array $data
     * @return Proxy
     */
    public function store(array $data)
    {
        $data = $this->__getData($data);
        return $this->proxy->create($data);
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
}