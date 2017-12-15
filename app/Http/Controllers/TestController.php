<?php

namespace OzSpy\Http\Controllers;

use Ixudra\Curl\Facades\Curl;
use OzSpy\Contracts\Models\Base\RetailerContract;
use OzSpy\Contracts\Models\Base\WebCategoryContract;
use OzSpy\Contracts\Models\Base\WebProductContract;
use OzSpy\Contracts\Models\Crawl\ProxyContract;
use OzSpy\Exceptions\SocialAuthExceptions\NullEmailException;
use Illuminate\Http\Request;
use OzSpy\Jobs\Crawl\WebCategory;
use OzSpy\Jobs\Crawl\WebProductList;
use OzSpy\Models\Base\Retailer;
use OzSpy\Models\Base\WebProduct;
use OzSpy\Models\Crawl\Proxy;
use OzSpy\Repositories\Scrapers\Proxies\ProxyNova;
use OzSpy\Repositories\Scrapers\Web\Kogan\WebCategoryScraper;

class TestController extends Controller
{
    public function index(WebProductContract $webProductRepo, WebCategoryContract $webCategoryRepo)
    {
        $result = WebProduct::createAll([
            ['name' => 'wtf'],
            ['name' => 'ftw'],
            ['name' => 'super'],
            ['name' => 'duper'],
        ]);
        dd($result);

//        $content = simplexml_load_string(file_get_contents('https://www.officeworks.com.au/sitemap-products.xml'));
//        $listInString = json_encode($content);
//        $listInArray = json_decode($listInString);
//        dd($listInArray, count($listInArray->url));


//        $response = Curl::to('https://www.thegoodguys.com.au/televisions/tv-cables-and-accessories')
//            ->withHeaders([
//                'Accept-Language: en-us',
//                'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.15) Gecko/20110303 Firefox/3.6.15',
//                'Connection: Keep-Alive',
//                'Cache-Control: no-cache',
//            ])
//            ->setCookieFile(storage_path('cookie/test'))
//            ->setCookieJar(storage_path('cookie/test'))
//            ->get();
//
//        if (!is_null($response)) {
//            preg_match('#SearchBasedNavigationDisplayJS.init\(\'(?:.*?)\',\'(.*?)\'\)#', $response, $matches);
//            $url = array_last($matches);
//            if (!is_null($url)) {
//                $parts = parse_url($url);
//                parse_str(array_get($parts, 'query'), $query);
//                $newResponse = Curl::to($url)
//                    ->withHeaders([
//                        'Accept-Language: en-us',
//                        'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.15) Gecko/20110303 Firefox/3.6.15',
//                        'Connection: Keep-Alive',
//                        'Cache-Control: no-cache',
//                    ])
//                    ->setCookieFile(storage_path('cookie/test'))
//                    ->setCookieJar(storage_path('cookie/test'))
//                    ->withData([
//                        "contentBeginIndex" => "0",
//                        "productBeginIndex" => "60",
//                        "beginIndex" => "60",
//                        "orderBy" => "",
//                        "facetId" => "",
//                        "pageView" => "grid",
//                        "resultType" => "products",
//                        "orderByContent" => "",
//                        "searchTerm" => "",
//                        "facet" => "",
//                        "facetLimit" => "",
//                        "minPrice" => "",
//                        "maxPrice" => "",
//                        "pageSize" => "",
//                        "storeId" => "900",
//                        "catalogId" => "30000",
//                        "langId" => "-1",
//                        "objectId" => str_replace('ProductListingView', '', array_get($query, 'ddkey')),
//                        "requesttype" => "ajax",
//                    ])
//                    ->post();
//                echo ($newResponse);exit();
//            }
//        }
//        $retailer = Retailer::findOrFail(9);
//        dispatch((new WebCategory($retailer))->onConnection('sync'));
//        return;

        $webCategory = $webCategoryRepo->get(1);
//        dispatch((new WebCategory($retailer))->onConnection('sync'));
        dispatch((new WebProductList($webCategory))->onConnection('sync'));

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
