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
 * Class UpdateService
 * @package OzSpy\Services\Entities\WebProduct
 */
class UpdateService extends WebProductServiceContract
{
    use Cacheable;

    /**
     * @param WebProduct $webProduct
     * @param array $data
     * @return bool
     */
    public function handle(WebProduct $webProduct, array $data)
    {
        $result = $this->webProductRepo->update($webProduct, $data);
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