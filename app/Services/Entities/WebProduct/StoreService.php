<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 21/01/2018
 * Time: 1:35 AM
 */

namespace OzSpy\Services\Entities\WebProduct;

/**
 * Class StoreService
 * @package OzSpy\Services\Entities\WebProduct
 */
class StoreService extends WebProductServiceContract
{
    /**
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function handle(array $data)
    {
        $webProduct = $this->webProductRepo->store($data);
        return $webProduct;
    }
}