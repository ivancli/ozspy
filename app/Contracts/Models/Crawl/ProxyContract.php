<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 13/11/2017
 * Time: 7:34 PM
 */

namespace OzSpy\Contracts\Models\Crawl;


use OzSpy\Models\Crawl\Proxy;

interface ProxyContract
{
    /**
     * get all proxies
     * @param bool $trashed
     * @return \Illuminate\Database\Eloquent\Collection|Proxy[]
     */
    public function all($trashed = false);

    /**
     * get proxy by id
     * @param $id
     * @param bool $throw
     * @return Proxy|null
     */
    public function get($id, $throw = true);

    /**
     * @param $ip
     * @param $port
     * @param bool $trashed
     * @return bool
     */
    public function exist($ip, $port, $trashed = false);

    /**
     * get random proxy
     * @param bool $trashed
     * @return null|Proxy
     */
    public function random($trashed = false);

    /**
     * create a new proxy
     * @param array $data
     * @return Proxy
     */
    public function store(array $data);

    /**
     * update an existing proxy
     * @param Proxy $proxy
     * @param array $data
     * @return bool
     */
    public function update(Proxy $proxy, array $data);

    /**
     * delete a proxy
     * @param Proxy $proxy
     * @param bool $force
     * @return bool
     */
    public function delete(Proxy $proxy, $force = false);

    /**
     * restore a proxy
     * @param Proxy $proxy
     * @return bool
     */
    public function restore(Proxy $proxy);

    /**
     * test validity of a proxy
     * @param Proxy $proxy
     * @return bool
     */
    public function test(Proxy $proxy);
}