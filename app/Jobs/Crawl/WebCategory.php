<?php

namespace OzSpy\Jobs\Crawl;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use OzSpy\Contracts\Models\Base\WebCategoryContract;
use OzSpy\Contracts\Scrapers\Webs\WebCategoryScraper;
use OzSpy\Models\Base\Retailer;
use OzSpy\Models\Base\WebCategory as WebCategoryModel;

class WebCategory implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Retailer
     */
    protected $retailer;

    /**
     * @var WebCategoryScraper
     */
    protected $categoryScraper;

    /**
     * @var WebCategoryContract
     */
    protected $categoryRepo;

    /**
     * @var WebCategoryModel
     */
    protected $categoryModel;

    /**
     * @var array
     */
    protected $storedCategories = [];

    /**
     * Create a new job instance.
     *
     * @param Retailer $retailer
     */
    public function __construct(Retailer $retailer)
    {
        $this->retailer = $retailer;
    }

    /**
     * Execute the job.
     *
     * @param WebCategoryContract $categoryRepo
     * @param WebCategoryModel $categoryModel
     * @return void
     */
    public function handle(WebCategoryContract $categoryRepo, WebCategoryModel $categoryModel)
    {
        $this->categoryRepo = $categoryRepo;

        $this->categoryModel = $categoryModel;

        $className = 'OzSpy\Repositories\Scrapers\Web\\' . $this->retailer->name . '\WebCategoryScraper';

        $this->categoryScraper = new $className($this->retailer);

        $this->categoryScraper->scrape();

        $categories = $this->categoryScraper->getCategories();

        foreach ($categories as $category) {
            $this->processSingleCategory($category);
        }
        $this->retailer = $this->retailer->fresh();
        $this->restoreCategories();
        $this->deleteCategories();

    }

    /**
     * process and store a single category
     * @param $category
     * @param WebCategoryModel|null $parentCategory
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Relations\HasMany|WebCategoryModel
     */
    protected function processSingleCategory($category, WebCategoryModel $parentCategory = null)
    {
        $categoryData = (array)$category;
        $categoryData = $this->__getData($categoryData);

        if (!is_null($parentCategory)) {
            if ($this->categoryRepo->exist($this->retailer, $category->name, $parentCategory, true)) {
                $storedCategory = $this->categoryRepo->findByName($this->retailer, $category->name, $parentCategory, true);
            }
        } else {
            if ($this->categoryRepo->exist($this->retailer, $category->name, null, true)) {
                $storedCategory = $this->categoryRepo->findByName($this->retailer, $category->name, null, true);
            }
        }
        if (!isset($storedCategory)) {
            $storedCategory = $this->categoryRepo->store($categoryData);
            $this->retailer->categories()->save($storedCategory);
        }
        if (!is_null($parentCategory)) {
            $parentCategory->childCategories()->save($storedCategory);
        }
        if (!empty($category->categories)) {
            foreach ($category->categories as $childCategory) {
                $this->processSingleCategory($childCategory, $storedCategory);
            }
        }
        $this->__signCategory($storedCategory);

        return $storedCategory;
    }

    /**
     * restore the deleted categories being found in page
     * @return void
     */
    protected function restoreCategories()
    {
        foreach ($this->storedCategories as $storedCategory) {
            if ($storedCategory->trashed()) {
                $this->categoryRepo->restore($storedCategory);
            }
        }
    }

    /**
     * delete the categories not found in page
     * @return void
     */
    protected function deleteCategories()
    {
        $categories = $this->retailer->categories;
        $notFoundCategories = $categories->diff($this->storedCategories);
        $notFoundCategoryIds = $notFoundCategories->pluck('id');
        $this->retailer->categories()->whereIn('id', $notFoundCategoryIds)->delete();
    }

    /**
     * filter provided data
     * @param array $data
     * @return array
     */
    private function __getData(array $data)
    {
        return array_only($data, $this->categoryModel->getFillable());
    }

    /**
     * add category object to array
     * @param WebCategoryModel $category
     * @return void
     */
    private function __signCategory(WebCategoryModel $category)
    {
        array_push($this->storedCategories, $category);
    }
}
