<?php

namespace OzSpy\Http\Controllers;

use OzSpy\Contracts\Models\Base\RetailerContract;
use OzSpy\Contracts\Models\Base\WebCategoryContract;
use OzSpy\Contracts\Models\Base\WebProductContract;
use OzSpy\Contracts\Models\Crawl\ProxyContract;
use OzSpy\Exceptions\SocialAuthExceptions\NullEmailException;
use Illuminate\Http\Request;
use OzSpy\Jobs\Crawl\WebCategory;
use OzSpy\Jobs\Crawl\WebProductList;
use OzSpy\Models\Base\Retailer;
use OzSpy\Models\Crawl\Proxy;
use OzSpy\Repositories\Scrapers\Proxies\ProxyNova;
use OzSpy\Repositories\Scrapers\Web\Kogan\WebCategoryScraper;

class TestController extends Controller
{
    public function index(WebProductContract $webProductRepo, WebCategoryContract $webCategoryRepo)
    {
        $retailer = Retailer::findOrFail(4);
        dispatch((new WebCategory($retailer))->onConnection('sync'));
//        dispatch((new WebProductList($webCategory))->onQueue('crawl-web-product-list')->onConnection('sync'));

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
