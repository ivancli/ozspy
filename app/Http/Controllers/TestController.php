<?php

namespace OzSpy\Http\Controllers;

use OzSpy\Contracts\Models\Base\RetailerContract;
use OzSpy\Contracts\Models\Crawl\ProxyContract;
use OzSpy\Exceptions\SocialAuthExceptions\NullEmailException;
use Illuminate\Http\Request;
use OzSpy\Models\Crawl\Proxy;
use OzSpy\Repositories\Scrapers\Proxies\ProxyNova;
use OzSpy\Repositories\Scrapers\Web\Kogan\WebCategoryScraper;

class TestController extends Controller
{
    public function index(RetailerContract $retailerContract)
    {
        $retailer = $retailerContract->get(1);
        $categoryScraper = new WebCategoryScraper($retailer);

        $categoryScraper->scrape();
        $categories = $categoryScraper->getCategories();
        dd($categories);
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
