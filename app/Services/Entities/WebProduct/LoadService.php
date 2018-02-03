<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 21/01/2018
 * Time: 1:34 AM
 */

namespace OzSpy\Services\Entities\WebProduct;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use OzSpy\Exceptions\SocialAuthExceptions\UnauthorisedException;
use OzSpy\Http\Resources\Base\WebProducts;
use OzSpy\Models\Base\Retailer;
use OzSpy\Models\Base\WebCategory;
use OzSpy\Models\Base\WebProduct;
use OzSpy\Traits\Entities\Cacheable;

/**
 * Class LoadService
 * @package OzSpy\Services\Entities\WebProduct
 */
class LoadService extends WebProductServiceContract
{
    use Cacheable;

    protected $relatedEntities = [
        WebProduct::class,
        WebCategory::class,
        Retailer::class,
    ];

    /**
     * @param array $data
     * @return WebProducts
     * @throws UnauthorisedException
     */
    public function handle(array $data = [])
    {
        if (is_null($this->authUser)) {
            throw new UnauthorisedException;
        }

        $this->setTags();

        return $this->remember($this->setKey($data), function () use ($data) {
            //set pagination data
            $webProductBuilder = $this->webProductRepo->builder();
            if (array_has($data, 'query')) {
                switch (array_get($data, 'query')) {
                    case 'price_change':
                        $webProductBuilder = $this->priceChangeBuilder($webProductBuilder);
                        break;
                    default:
                }
            }

            if (array_has($data, 'order') && array_has(array_get($data, 'order'), 'attr')) {
                $order = array_get($data, 'order');
                $column = array_get($order, 'attr');
                $direction = array_get($order, 'direction', 'asc');
                $webProductBuilder->orderBy($column, $direction);
            }


            $webProductBuilder = $webProductBuilder->join('web_historical_prices', 'web_products.id', 'web_historical_prices.web_product_id');
            $sql = $webProductBuilder->toSql();
//            $sql = $webProductBuilder->with(['webCategories', 'retailer', 'webHistoricalPrices', 'recentWebHistoricalPrice', 'previousWebHistoricalPrice'])->toSql();
            print_r($sql);exit();

            return new WebProducts($webProductBuilder->with(['webCategories', 'retailer', 'webHistoricalPrices', 'recentWebHistoricalPrice', 'previousWebHistoricalPrice'])->paginate(array_get($data, 'per_page', 15)));
        });
    }

    protected function priceChangeBuilder(Model $builder = null)
    {
        if (is_null($builder)) {
            $builder = $this->webProductRepo->builder();
        }
        $builder->join('web_historical_prices', 'web_products.id', 'web_historical_prices.web_product_id');


        $builder = $builder->whereHas('recentWebHistoricalPrice')->whereHas('previousWebHistoricalPrice');
        return $builder;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function setKey(array $data)
    {
        return [
            'Path' => self::class,
            'Request' => $data
        ];
    }

    /**
     * set tag for caching
     */
    protected function setTags()
    {
        $this->authBasedTag();
        $this->nameBasedTag($this->relatedEntities);
    }
}