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

    protected $eagerLoadRelations = [
        'webCategories', 'retailer',
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


            $query = array_get($data, 'query');

            switch (array_get($data, 'query')) {
                case 'price_change':
                    $webProductsBuilder = $this->priceChange();
                    break;
                default:
                    $webProductsBuilder = $this->default();
            }

            if (array_has($data, 'order') && array_has(array_get($data, 'order'), 'attr')) {
                $order = array_get($data, 'order');
                $column = array_get($order, 'attr');
                $direction = array_get($order, 'direction', 'asc');
                $webProductsBuilder->orderBy($column, $direction);
            }

            if (array_has($data, 'attributes') && is_array(array_get($data, 'attributes'))) {
                foreach (array_get($data, 'attributes') as $attribute) {
                    switch ($attribute) {
                        case 'recent_price':
                            $this->eagerLoadRelations[] = 'recentWebHistoricalPrice';
                            break;
                        case 'previous_price':
                            $this->eagerLoadRelations[] = 'previousWebHistoricalPrice';
                            break;
                    }
                }
            }

            $webProductsBuilder = $webProductsBuilder->with(['webCategories', 'retailer', 'webHistoricalPrices', 'recentWebHistoricalPrice', 'previousWebHistoricalPrice']);

            return new WebProducts($webProductsBuilder->paginate(array_get($data, 'per_page', 15)));
        });
    }

    protected function default()
    {
        $webProductsBuilder = $this->webProductRepo->builder();
        return $webProductsBuilder;
    }

    protected function priceChange()
    {
        $webProductsBuilder = $this->webProductRepo->builder();


        return $webProductsBuilder;
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