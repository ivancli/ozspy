<?php

namespace OzSpy\Jobs\Models\WebCategory;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use OzSpy\Contracts\Models\Base\WebCategoryContract;
use OzSpy\Exceptions\Models\DuplicateCategoryException;
use OzSpy\Models\Base\Retailer;
use OzSpy\Models\Base\WebCategory;

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
     * @var WebCategory
     */
    protected $webCategoryModel;

    /**
     * @var WebCategory
     */
    protected $parentCategory;

    /**
     * Create a new job instance.
     *
     * @param Retailer $retailer
     * @param array $data
     * @param WebCategory|null $parentCategory
     */
    public function __construct(Retailer $retailer, array $data, WebCategory $parentCategory = null)
    {
        $this->webCategoryModel = new WebCategory;

        $this->retailer = $retailer;

        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @param WebCategoryContract $webCategoryRepo
     * @return void
     * @throws DuplicateCategoryException
     */
    public function handle(WebCategoryContract $webCategoryRepo)
    {
        $categoryData = $this->__getData($this->data);

        if (!is_null($this->parentCategory)) {
            if (!$webCategoryRepo->exist($this->retailer, array_get($categoryData, 'name'), $this->parentCategory, true)) {
                $storedCategory = $webCategoryRepo->store($categoryData);
                $this->retailer->webCategories()->save($storedCategory);
                $this->parentCategory->childCategories()->save($storedCategory);
            } else {
                throw new DuplicateCategoryException;
            }
        } else {
            if (!$webCategoryRepo->exist($this->retailer, array_get($categoryData, 'name'), null, true)) {
                $storedCategory = $webCategoryRepo->store($categoryData);
                $this->retailer->webCategories()->save($storedCategory);
            } else {
                throw new DuplicateCategoryException;
            }
        }
    }

    private function __getData(array $data)
    {
        return array_only($data, $this->webCategoryModel->getFillable());
    }
}
