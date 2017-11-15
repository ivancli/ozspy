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
     * @return \Illuminate\Database\Eloquent\Collection|Proxy[]
     */
    public function all();

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
     * @return bool
     */
    public function exist($ip, $port);

    /**
     * get random proxy
     * @param bool $only_active
     * @return null|Proxy
     */
    public function random($only_active = true);

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
     * @return bool
     */
    public function delete(Proxy $proxy);

    /**
     * test validity of a proxy
     * @param Proxy $proxy
     * @return bool
     */
    public function test(Proxy $proxy);
}