<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 21/01/2018
 * Time: 1:35 AM
 */

namespace OzSpy\Services\Entities\WebProduct;


use OzSpy\Contracts\Models\Base\WebProductContract;
use OzSpy\Services\ServiceContract;

/**
 * Class WebProductServiceContract
 * @package OzSpy\Services\Entities\WebProduct
 */
abstract class WebProductServiceContract extends ServiceContract
{

    /**
     * @var WebProductContract
     */
    protected $webProductRepo;

    /**
     * UserServiceContract constructor.
     * @param WebProductContract $webProductContract
     */
    public function __construct(WebProductContract $webProductContract)
    {
        parent::__construct();

        $this->webProductRepo = $webProductContract;
    }
}