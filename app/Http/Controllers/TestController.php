<?php

namespace OzSpy\Http\Controllers;

use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use OzSpy\Contracts\Models\Base\RetailerContract;
use OzSpy\Contracts\Models\Base\WebCategoryContract;
use OzSpy\Contracts\Models\Base\WebProductContract;
use OzSpy\Http\Resources\Base\WebProducts;
use OzSpy\Models\Auth\User;
use OzSpy\Models\Base\WebProduct;
use OzSpy\Services\Entities\WebProduct\LoadService;
use OzSpy\Traits\Commands\Optionable;


class TestController extends Controller
{
    use Optionable;

    public function index(RetailerContract $retailerRepo, WebCategoryContract $webCategoryRepo, WebProductContract $webProductRepo, LoadService $loadService)
    {
//        DB::enableQueryLog();
//        WebProduct::with(['webCategories', 'retailer', 'webHistoricalPrices', 'recentWebHistoricalPrice'])->paginate();
//        dd(DB::getQueryLog());
//        DB::enableQueryLog();
////        $builder = WebProduct::whereHas('recentWebHistoricalPrice');
//        dump(new WebProducts(WebProduct::with(['webCategories', 'retailer', 'webHistoricalPrices', 'recentWebHistoricalPrice'])->paginate()));
//        dd(DB::getQueryLog());

//        $data = $loadService->handle();
//        dd($data);

//        $data = $loadService->handle();
//        return new Response($data);

//        $retailer = $retailerRepo->get(11);
//
//        $filePath = storage_path('app/scraper/scrapers/' . $retailer->abbreviation . '/categories.js');
//        $execFilePath = storage_path('app/scraper/index.js');
//        if (file_exists($filePath)) {
//            $options = [
//                'retailer' => urlencode($retailer->toJson()),
//                'scraper' => 'categories'
//            ];
//
//            $optionStr = $this->format($options)->toString()->getOptionsStr();
//            dd("node $execFilePath {$optionStr}");
//        }


//        $webCategory = $webCategoryRepo->get(10667);
//        $retailer = $webCategory->retailer;
//        $filePath = storage_path('app/scraper/scrapers/' . $retailer->abbreviation . '/products.js');
//        $execFilePath = storage_path('app/scraper/index.js');
//        if (file_exists($filePath)) {
//            $options = [
//                'category' => urlencode($webCategory->toJson()),
//                'retailer' => urlencode($retailer->toJson()),
//                'scraper' => 'products',
//            ];
//
//            $optionStr = $this->format($options)->toString()->getOptionsStr();
//
//            dd("node --expose-gc $execFilePath {$optionStr}");
//        }

//        $webProduct = $webProductRepo->get(183002);
//        $retailer = $webProduct->retailer;
//        $filePath = storage_path('app/scraper/scrapers/' . $retailer->abbreviation . '/images.js');
//        $execFilePath = storage_path('app/scraper/index.js');
//        if (file_exists($filePath)) {
//            $options = [
//                'product' => "'" . $webProduct->toJson() . "'",
//                'retailer' => "'" . $retailer->toJson() . "'",
//                'scraper' => 'images',
//            ];
//
//            $optionStr = $this->format($options)->toString()->getOptionsStr();
//
//            dd("node $execFilePath {$optionStr}");
//        }


//        $webCategory = $webCategoryRepo->get(1601);
//        set_time_limit(99999);
//        $this->dispatch((new \OzSpy\Jobs\Scrape\WebProduct($webCategory))->onConnection('sync'));
//        $this->dispatch((new \OzSpy\Jobs\Update\WebCategory($retailer))->onConnection('sync'));
//        $this->dispatch((new \OzSpy\Jobs\Update\WebProduct($webCategory))->onConnection('sync'));
        auth()->login(User::findOrFail(1));
        $loadService->handle(request()->all())->response();
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
