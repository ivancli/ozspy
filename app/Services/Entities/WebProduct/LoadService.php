<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 21/01/2018
 * Time: 1:34 AM
 */

namespace OzSpy\Services\Entities\WebProduct;

use OzSpy\Exceptions\SocialAuthExceptions\UnauthorisedException;
use OzSpy\Traits\Responses\Pageable;

/**
 * Class LoadService
 * @package OzSpy\Services\Entities\WebProduct
 */
class LoadService extends WebProductServiceContract
{
    use Pageable;

    /**
     * @param array $data
     * @return array
     * @throws UnauthorisedException
     */
    public function handle(array $data = [])
    {
        $this->data = $this->webProductRepo->builder()->first();

        if (is_null($this->authUser)) {
            throw new UnauthorisedException;
        }

        //set pagination data
        $this->pageableSet($data);


        if ($this->authUser->isStaff()) {
            $webProductBuilder = $this->webProductRepo->builder();
        } else {
            /*TODO change this part to link with user model*/
            $webProductBuilder = $this->webProductRepo->builder();
        }

        $this->pageablePrepare($webProductBuilder);


        $webProducts = $webProductBuilder->get();

        $this->data = $webProducts;

        $result = $this->pageableComposer();

        return $result;
    }
}