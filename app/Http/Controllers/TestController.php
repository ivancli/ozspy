<?php

namespace OzSpy\Http\Controllers;

use Ixudra\Curl\Facades\Curl;
use OzSpy\Contracts\Models\Auth\UserContract;
use OzSpy\Contracts\Models\Base\RetailerContract;
use OzSpy\Contracts\Models\Base\WebCategoryContract;
use OzSpy\Contracts\Models\Base\WebProductContract;
use OzSpy\Contracts\Models\Crawl\ProxyContract;
use OzSpy\Exceptions\SocialAuthExceptions\NullEmailException;
use Illuminate\Http\Request;
use OzSpy\Jobs\Crawl\WebCategory;
use OzSpy\Jobs\Crawl\WebProductList;
use OzSpy\Jobs\Models\WebProduct\UpdateOrStore;
use OzSpy\Models\Base\Retailer;
use OzSpy\Models\Base\WebProduct;
use OzSpy\Models\Crawl\Proxy;
use OzSpy\Repositories\Scrapers\Proxies\ProxyNova;
use OzSpy\Repositories\Scrapers\Web\Kogan\WebCategoryScraper;
use OzSpy\Traits\Commands\Optionable;

class TestController extends Controller
{
    use Optionable;

    public function index(UserContract $userContract)
    {
        $user = $userContract->findBy('id', 1)->first();
        auth()->login($user);
        return view('test');
//        $retailer = $retailerRepo->get(3);
//
//        $filePath = storage_path('app/scraper/scrapers/' . $retailer->abbreviation . '/categories.js');
//        $execFilePath = storage_path('app/scraper/index.js');
//        if (file_exists($filePath)) {
//            $options = [
//                'retailer' => "'" . $retailer->toJson() . "'",
//                'scraper' => 'categories'
//            ];
//
//            $optionStr = $this->format($options)->toString()->getOptionsStr();
//            dd("node $execFilePath {$optionStr}");
//        }


        $webCategory = $webCategoryRepo->get(4147);
        $filePath = storage_path('app/scraper/scrapers/hn/products.js');
        $execFilePath = storage_path('app/scraper/index.js');
        if (file_exists($filePath)) {
            $options = [
                'category' => "'" . $webCategory->toJson() . "'",
                'retailer' => "'" . $webCategory->retailer->toJson() . "'",
                'scraper' => 'products',
            ];

            $optionStr = $this->format($options)->toString()->getOptionsStr();

            dd("node $execFilePath {$optionStr}");
        }


//        $webCategory = $webCategoryRepo->get(1601);
//        set_time_limit(99999);
//        $this->dispatch((new \OzSpy\Jobs\Scrape\WebProduct($webCategory))->onConnection('sync'));
//        $this->dispatch((new \OzSpy\Jobs\Update\WebCategory($retailer))->onConnection('sync'));
//        $this->dispatch((new \OzSpy\Jobs\Update\WebProduct($webCategory))->onConnection('sync'));
        return view('test');
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
