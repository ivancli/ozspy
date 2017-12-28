<?php

namespace OzSpy\Jobs\Models\WebProduct;

use Closure;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use OzSpy\Contracts\Models\Base\WebProductContract;
use OzSpy\Models\Base\Retailer;
use OzSpy\Models\Base\WebCategory;
use OzSpy\Models\Base\WebProduct;

class Store implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Retailer
     */
    protected $retailer;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var WebCategory
     */
    protected $webCategory;

    /**
     * @var WebProduct
     */
    protected $webProductModel;

    /**
     * @var Closure
     */
    protected $callback;

    /**
     * Create a new job instance.
     *
     * @param Retailer $retailer
     * @param array $data
     * @param WebCategory $webCategory
     * @param Closure $callback
     */
    public function __construct(Retailer $retailer, array $data, WebCategory $webCategory = null, Closure $callback = null)
    {
        $this->webProductModel = new WebProduct;

        $this->retailer = $retailer;

        $this->data = $this->__getData($data);

        $this->webCategory = $webCategory;

        $this->callback = $callback;
    }

    /**
     * Execute the job.
     *
     * @param WebProductContract $webProductRepo
     * @return void
     */
    public function handle(WebProductContract $webProductRepo)
    {

        if (array_has($this->data, 'retailer_product_id')) {
            $existingWebProduct = $webProductRepo->findBy($this->retailer, 'retailer_product_id', array_get($this->data, 'retailer_product_id'))->first();
        } elseif (array_has($this->data, 'slug')) {
            $existingWebProduct = $webProductRepo->findBy($this->retailer, 'slug', array_get($this->data, 'slug'))->first();
        }

        if (!isset($existingWebProduct) || is_null($existingWebProduct)) {
            $existingWebProduct = $webProductRepo->store($this->data);
        }

        if (!is_null($this->callback)) {
            ($this->callback)($existingWebProduct);
        }
    }

    private function __getData(array $data)
    {
        return array_only($data, $this->webProductModel->getFillable());
    }
}
