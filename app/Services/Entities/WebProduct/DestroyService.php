<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 21/01/2018
 * Time: 1:35 AM
 */

namespace OzSpy\Services\Entities\WebProduct;

use OzSpy\Models\Base\WebProduct;

/**
 * Class DestroyService
 * @package OzSpy\Services\Entities\WebProduct
 */
class DestroyService extends WebProductServiceContract
{
    /**
     * @param WebProduct $webProduct
     * @return bool
     */
    public function handle(WebProduct $webProduct)
    {
        $result = $this->webProductRepo->delete($webProduct);
        return $result;
    }
}