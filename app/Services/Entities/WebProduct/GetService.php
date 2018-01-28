<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 28/01/2018
 * Time: 2:41 PM
 */

namespace OzSpy\Services\Entities\WebProduct;


use OzSpy\Models\Base\WebProduct;
use OzSpy\Http\Resources\Base\WebProduct as WebProductResource;

class GetService extends WebProductServiceContract
{
    /**
     * @param WebProduct $webProduct
     * @return WebProductResource
     */
    public function handle(WebProduct $webProduct)
    {
        return new WebProductResource($webProduct);
    }
}