<?php

namespace OzSpy\Http\Controllers;

use OzSpy\Contracts\Models\Crawl\ProxyContract;
use OzSpy\Exceptions\SocialAuthExceptions\NullEmailException;
use Illuminate\Http\Request;
use OzSpy\Models\Crawl\Proxy;
use OzSpy\Repositories\Scrapers\Proxies\ProxyNova;

class TestController extends Controller
{
    public function index(ProxyContract $proxyRepo)
    {
        $content = file_get_contents(base_path('vendor/mledoze/countries/dist/countries.json'));
        $jsonObject = json_decode($content);
        dd(($jsonObject));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
