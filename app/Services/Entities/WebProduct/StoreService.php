<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 21/01/2018
 * Time: 1:35 AM
 */

namespace OzSpy\Services\Entities\WebProduct;

use OzSpy\Http\Resources\Base\WebProduct;
use OzSpy\Traits\Entities\Cacheable;

/**
 * Class StoreService
 * @package OzSpy\Services\Entities\WebProduct
 */
class StoreService extends WebProductServiceContract
{
    use Cacheable;

    /**
     * @param array $data
     * @return WebProduct
     */
    public function handle(array $data)
    {
        $webProduct = $this->webProductRepo->store($data);
        return new WebProduct($webProduct);
    }

    /**
     * clear cache
     */
    protected function clearCache()
    {
        $this->setTags();
        $this->flush();
    }

    /**
     * set tag for removal
     */
    protected function setTags()
    {
        /*todo check role and flush based on tags*/
        $this->authBasedTag();
    }
}