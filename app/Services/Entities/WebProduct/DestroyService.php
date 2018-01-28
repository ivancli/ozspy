<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 21/01/2018
 * Time: 1:35 AM
 */

namespace OzSpy\Services\Entities\WebProduct;

use OzSpy\Models\Base\WebProduct;
use OzSpy\Traits\Entities\Cacheable;

/**
 * Class DestroyService
 * @package OzSpy\Services\Entities\WebProduct
 */
class DestroyService extends WebProductServiceContract
{
    use Cacheable;

    /**
     * @param WebProduct $webProduct
     * @return bool
     */
    public function handle(WebProduct $webProduct)
    {
        $result = $this->webProductRepo->delete($webProduct);
        $this->clearCache();
        return $result;
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