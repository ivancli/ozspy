<?php

namespace Tests\Unit\Models\Base;

use OzSpy\Models\Base\WebCategory;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Unit\Models\ModelTestCase;

class WebCategoryTest extends ModelTestCase
{
    use RefreshDatabase;

    /**
     * Test finding model by ID
     * @return void
     */
    public function testFind()
    {
        $webCategory = factory(WebCategory::class)->create([
            'deleted_at' => null,
        ]);
        try {
            $resultWebCategory = WebCategory::find($webCategory->getKey());
            /*web category found*/
            $this->assertTrue(!is_null($resultWebCategory));
        } catch (\Exception $e) {
            /*shouldn't throw exception as web category exists*/
            $this->assertFalse(true);
        }
    }

    /**
     * Test finding model by ID and throw exception if not found
     * @return void
     */
    public function testFindOrFail()
    {
        $webCategory = factory(WebCategory::class)->create([
            'deleted_at' => null,
        ]);
        try {
            $resultWebCategorySuccess = WebCategory::findOrFail($webCategory->getKey());
            /*web category found*/
            $this->assertTrue(!is_null($resultWebCategorySuccess));
        } catch (\Exception $e) {
            /*shouldn't throw exception as web category exists*/
            $this->assertFalse(true);
        }

        try {
            $resultWebCategoryFailed = WebCategory::findOrFail($webCategory->getKey() + 1);
            /*should throw Model Not Found Exception*/
            $this->assertFalse(true);
        } catch (\Exception $e) {
            /*throwing exception as Model Not Found*/
            $this->assertTrue(true);
        }
    }

    /**
     * Test fetching all models
     * @return void
     */
    public function testAll()
    {
        $numberOfWebCategoriesToCreate = rand(1, 100);

        $webCategory = factory(WebCategory::class, $numberOfWebCategoriesToCreate)->create();

        /*make sure deleted web categories are not counted*/
        $webCategory = $webCategory->filter(function (WebCategory $webCategory) {
            return is_null($webCategory->deleted_at);
        });

        $fetchedWebCategories = WebCategory::all();
        $this->assertTrue($webCategory->count() === $fetchedWebCategories->count());
        $fetchedWebCategories->each(function ($fetchWebCategory) use ($webCategory) {
            $matchedWebCategories = $webCategory->filter(function ($webCategory) use ($fetchWebCategory) {
                return $webCategory->getKey() === $fetchWebCategory->getKey();
            });
            $this->assertTrue($matchedWebCategories->count() === 1, "web category with ID [{$fetchWebCategory->getKey()}] cannot be found.");
        });
    }

    /**
     * Test single fetch result's fillable attributes
     * @return void
     */
    public function testSingleFillables()
    {
        $webCategory = factory(WebCategory::class)->create([
            'deleted_at' => null,
        ]);

        $fetchedWebCategory = WebCategory::findOrFail($webCategory->getKey());
        $fillableAttributes = $fetchedWebCategory->getFillable();
        if (count($fillableAttributes) > 0) {
            foreach ($fillableAttributes as $fillableAttribute) {
                $this->assertTrue(isset($webCategory->$fillableAttribute), "Attribute [{$fillableAttribute}] is missing in web category {$webCategory->getKey()}");
            }
        } else {
            $this->assertTrue(true);
        }
    }

    /**
     * Test all fetch results' fillable attributes
     * @return void
     */
    public function testAllFillables()
    {
        // TODO: Implement testAllFillables() method.
    }

    /**
     * Test single fetch result's hidden attributes
     * @return mixed
     */
    public function testSingleHiddens()
    {
        // TODO: Implement testSingleHiddens() method.
    }

    /**
     * Test all fetch results' hidden attributes
     * @return void
     */
    public function testAllHiddens()
    {
        // TODO: Implement testAllHiddens() method.
    }

    /**
     * Test single fetch result's appended attributes
     * @return void
     */
    public function testSingleAppends()
    {
        // TODO: Implement testSingleAppends() method.
    }

    /**
     * Test all fetch results' appended attributes
     * @return void
     */
    public function testAllAppends()
    {
        // TODO: Implement testAllAppends() method.
    }

    /**
     * Test storing a new model
     * @return void
     */
    public function testStore()
    {
        // TODO: Implement testStore() method.
    }

    /**
     * Test updating an existing model
     * @return void
     */
    public function testUpdate()
    {
        // TODO: Implement testUpdate() method.
    }

    /**
     * Test deleting an existing model
     * @return void
     */
    public function testDelete()
    {
        // TODO: Implement testDelete() method.
    }
}
