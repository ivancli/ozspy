<?php

namespace OzSpy\Jobs\Models\WebCategory;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use OzSpy\Contracts\Models\Base\WebCategoryContract;
use OzSpy\Models\Base\Retailer;
use OzSpy\Models\Base\WebCategory;

/**
 * Update existing WebCategory or Store a new WebCategory
 * Class UpdateOrStore
 * @package OzSpy\Jobs\Models\WebCategory
 */
class UpdateOrStore implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var WebCategory
     */
    protected $webCategoryModel;

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
    protected $parentCategory;

    /**
     * UpdateOrStore constructor.
     * @param Retailer $retailer
     * @param array $data
     * @param WebCategory|null $parentCategory
     */
    public function __construct(Retailer $retailer, array $data, WebCategory $parentCategory = null)
    {
        $this->retailer = $retailer;

        $this->data = $data;

        $this->parentCategory = $parentCategory;
    }

    /**
     * Execute the job.
     *
     * @param WebCategoryContract $webCategoryRepo
     * @return void
     */
    public function handle(WebCategoryContract $webCategoryRepo)
    {
        $this->webCategoryModel = new WebCategory;

        $categoryData = $this->__getData($this->data);

        if (!is_null($this->parentCategory)) {
            if ($webCategoryRepo->exist($this->retailer, array_get($categoryData, 'name'), $this->parentCategory, true)) {
                $storedCategory = $webCategoryRepo->findByName($this->retailer, array_get($categoryData, 'name'), $this->parentCategory, true);
                $webCategoryRepo->update($storedCategory, $categoryData);
            }
        } else {
            if ($webCategoryRepo->exist($this->retailer, array_get($categoryData, 'name'), null, true)) {
                $storedCategory = $webCategoryRepo->findByName($this->retailer, array_get($categoryData, 'name'), null, true);
                $webCategoryRepo->update($storedCategory, $categoryData);
            }
        }

        if (!isset($storedCategory)) {
            $storedCategory = $webCategoryRepo->store($categoryData);
            $this->retailer->webCategories()->save($storedCategory);
        }
        if (!is_null($this->parentCategory)) {
            $this->parentCategory->childCategories()->save($storedCategory);
        }

        $childCategories = array_get($this->data, 'categories');

        if (is_array($childCategories) && !empty($childCategories)) {
            foreach ($childCategories as $childCategory) {
                dispatch((new UpdateOrStore($this->retailer, (array)$childCategory, $storedCategory)));
            }
        }
    }

    private function __getData(array $data)
    {
        return array_only($data, $this->webCategoryModel->getFillable());
    }
}
