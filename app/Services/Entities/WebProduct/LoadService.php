<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 21/01/2018
 * Time: 1:34 AM
 */

namespace OzSpy\Services\Entities\WebProduct;

use Illuminate\Database\Eloquent\Builder;
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

            switch (array_get($data, 'query')) {
                case 'recent_price':
                    $webProductsBuilder = $this->recentPrice();
                    break;
                case 'price_change':
                    $webProductsBuilder = $this->priceChange();
                    break;
                case 'price_drop':
                    $webProductsBuilder = $this->priceDrop();
                    break;
                case 'price_raise':
                    $webProductsBuilder = $this->priceRaise();
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

            if (array_has($data, 'filter') && is_array(array_get($data, 'filter'))) {
                $filter = array_get($data, 'filter');
                $webProductsBuilder = $this->filter($webProductsBuilder, $filter);
            }

            $webProductsBuilder = $webProductsBuilder->with(['webCategories', 'retailer']);

            return new WebProducts($webProductsBuilder->paginate(array_get($data, 'per_page', 15)));
        });
    }

    /**
     * @param Builder|Model $builder
     * @param array $data
     * @return $this|Builder|static
     */
    protected function filter($builder, array $data)
    {
        foreach ($data as $attr => $value) {
            switch ($attr) {
                case 'category':
                    $builder = $builder->whereHas('webCategories', function (Builder $builder) use ($value) {
                        if (is_array($value)) {
                            $builder->whereIn('name', $value);
                        } else {
                            $builder->where('name', $value);
                        }
                    });
                    break;
                case 'retailer':
                    $builder = $builder->whereHas('retailer', function (Builder $builder) use ($value) {
                        if (is_array($value)) {
                            $builder->whereIn('name', $value);
                        } else {
                            $builder->where('name', $value);
                        }
                    });
                    break;
                case 'max_recent_price':
                    $builder = $builder->hasRecentPrice()->where('recent_price', '<=', $value);
                    break;
                case 'max_previous_price':
                    $builder = $builder->hasPreviousPrice()->where('previous_price', '<=', $value);
                    break;
                case 'min_recent_price':
                    $builder = $builder->hasRecentPrice()->where('recent_price', '>=', $value);
                    break;
                case 'min_previous_price':
                    $builder = $builder->hasPreviousPrice()->where('previous_price', '>=', $value);
                    break;
                default:
                    $builder = $builder->where($attr, $value);
            }
        }
        return $builder;
    }

    /**
     * @return Builder|Model
     */
    protected function default()
    {
        $webProductsBuilder = $this->webProductRepo->builder();
        return $webProductsBuilder;
    }

    /**
     * @return Builder
     */
    protected function recentPrice()
    {
        $webProductsBuilder = $this->webProductRepo->builder()->hasRecentPrice();
        return $webProductsBuilder;
    }

    /**
     * @return Builder
     */
    protected function priceChange()
    {
        $webProductsBuilder = $this->webProductRepo->builder()->hasPreviousPrice();
        return $webProductsBuilder;
    }

    /**
     * @return Builder
     */
    protected function priceDrop()
    {
        $webProductsBuilder = $this->webProductRepo->builder()->hasPriceDrop();
        return $webProductsBuilder;
    }

    /**
     * @return Builder
     */
    protected function priceRaise()
    {
        $webProductsBuilder = $this->webProductRepo->builder()->hasPriceRaise();
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