<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 21/01/2018
 * Time: 1:34 AM
 */

namespace OzSpy\Services\Entities\WebProduct;

use OzSpy\Exceptions\SocialAuthExceptions\UnauthorisedException;
use OzSpy\Http\Resources\Base\WebProductCollection;
use OzSpy\Http\Resources\Base\WebProducts;
use OzSpy\Models\Base\Retailer;
use OzSpy\Models\Base\WebCategory;
use OzSpy\Models\Base\WebProduct;
use OzSpy\Traits\Entities\Cacheable;
use OzSpy\Traits\Responses\Pageable;

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
     * @return array
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
            if ($this->authUser->isStaff()) {
                $webProductBuilder = $this->webProductRepo->builder();
            } else {
                /*TODO change this part to link with user model*/
                $webProductBuilder = $this->webProductRepo->builder();
            }

            return new WebProducts($webProductBuilder->paginate(array_get($data, 'per_page', 15)));
        });
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