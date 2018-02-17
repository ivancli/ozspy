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
                $this->assertTrue(isset($webCategory->$fillableAttribute) || is_null($webCategory->$fillableAttribute), "Attribute [{$fillableAttribute}] is missing in web category {$webCategory->getKey()}");
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
        $numberOfWebCategoriesToCreate = rand(1, 10);
        $webCategories = factory(WebCategory::class, $numberOfWebCategoriesToCreate)->create();
        $fetchedWebCategories = WebCategory::all();
        if ($fetchedWebCategories->count() > 0) {
            $fetchedWebCategories->each(function (WebCategory $webCategory) {
                $fillableAttributes = $webCategory->getFillable();
                if (count($fillableAttributes) > 0) {
                    foreach ($fillableAttributes as $fillableAttribute) {
                        $this->assertTrue(isset($webCategory->$fillableAttribute) || is_null($webCategory->$fillableAttribute), "Attribute [{$fillableAttribute}] is missing in web category {$webCategory->getKey()}");
                    }
                } else {
                    $this->assertTrue(true);
                }
            });
        } else {
            $this->assertTrue(true);
        }
    }

    /**
     * Test single fetch result's hidden attributes
     * @return mixed
     */
    public function testSingleHiddens()
    {
        $webCategory = factory(WebCategory::class)->create([
            'deleted_at' => null,
        ]);

        $fetchedWebCategory = WebCategory::findOrFail($webCategory->getKey());
        $webCategoryArray = $fetchedWebCategory->toArray();
        $hiddenAttributes = $fetchedWebCategory->getHidden();
        if (count($hiddenAttributes) > 0) {
            foreach ($hiddenAttributes as $hiddenAttribute) {
                $this->assertArrayNotHasKey($hiddenAttribute, $webCategoryArray, "Attribute [{$hiddenAttribute}] appears in web category {$webCategory->getKey()}");
            }
        } else {
            $this->assertTrue(true);
        }
    }

    /**
     * Test all fetch results' hidden attributes
     * @return void
     */
    public function testAllHiddens()
    {
        $numberOfWebCategoriesToCreate = rand(1, 10);
        $webCategories = factory(WebCategory::class, $numberOfWebCategoriesToCreate)->create();
        $fetchedWebCategories = WebCategory::all();
        if ($fetchedWebCategories->count() > 0) {
            $fetchedWebCategories->each(function (WebCategory $webCategory) {
                $webCategoryArray = $webCategory->toArray();
                $hiddenAttributes = $webCategory->getHidden();
                if (count($hiddenAttributes) > 0) {
                    foreach ($hiddenAttributes as $hiddenAttribute) {
                        $this->assertArrayNotHasKey($hiddenAttribute, $webCategoryArray, "Attribute [{$hiddenAttribute}] appears in web category {$webCategory->getKey()}");
                    }
                } else {
                    $this->assertTrue(true);
                }
            });
        } else {
            $this->assertTrue(true);
        }
    }

    /**
     * Test single fetch result's appended attributes
     * @return void
     */
    public function testSingleAppends()
    {
        $webCategory = factory(WebCategory::class)->create([
            'deleted_at' => null,
        ]);

        $fetchedWebCategory = WebCategory::findOrFail($webCategory->getKey());
        $webCategoryArray = $fetchedWebCategory->toArray();
        $appendedAttributes = WebCategory::getAppends();
        if (count($appendedAttributes) > 0) {
            foreach ($appendedAttributes as $appendedAttribute) {
                $this->assertArrayHasKey($appendedAttribute, $webCategoryArray, "Attribute [{$appendedAttribute}] is missing in web category {$webCategory->getKey()}");
            }
        } else {
            $this->assertTrue(true);
        }
    }

    /**
     * Test all fetch results' appended attributes
     * @return void
     */
    public function testAllAppends()
    {
        $numberOfWebCategoriesToCreate = rand(1, 10);
        $webCategories = factory(WebCategory::class, $numberOfWebCategoriesToCreate)->create();
        $fetchedWebCategories = WebCategory::all();

        $appendedAttributes = WebCategory::getAppends();

        if ($fetchedWebCategories->count() > 0 && count($appendedAttributes) > 0) {
            $fetchedWebCategories->each(function (WebCategory $webCategory) use ($appendedAttributes) {
                $webCategoryArray = $webCategory->toArray();
                foreach ($appendedAttributes as $appendedAttribute) {
                    $this->assertArrayHasKey($appendedAttribute, $webCategoryArray, "Attribute [{$appendedAttribute}] is missing in web category {$webCategory->getKey()}");
                }
            });
        } else {
            $this->assertTrue(true);
        }
    }

    /**
     * Test storing a new model
     * @return void
     */
    public function testStore()
    {
        $faker = $this->app->make(\Faker\Generator::class);

        $categoryName = $faker->sentence(rand(1, 4));
        $webCategory = WebCategory::create([
            'name' => $categoryName,
            'slug' => $faker->boolean() ? str_slug($categoryName) : null,
            'field' => $faker->boolean(5) ? str_slug($faker->sentence(rand(1, 4))) : null,
            'url' => $faker->url,
            'active' => $faker->boolean(70),
            'last_crawled_products_count' => $faker->boolean() ? $faker->numberBetween() : null,
        ]);

        $this->assertTrue(WebCategory::count() === 1);
        $resultWebCategory = WebCategory::findOrFail($webCategory->getKey());
        foreach ($webCategory->getFillable() as $fillable) {
            $this->assertTrue($webCategory->$fillable === $resultWebCategory->$fillable, "Fillable {$fillable} expected to be {$webCategory->$fillable} but got {$resultWebCategory->$fillable}");
        }
    }

    /**
     * Test updating an existing model
     * @return void
     */
    public function testUpdate()
    {
        $faker = $this->app->make(\Faker\Generator::class);
        $webCategory = factory(WebCategory::class)->create([
            'deleted_at' => null,
        ]);
        $categoryName = $faker->sentence(rand(1, 4));
        $webCategory->update([
            'name' => $categoryName,
            'slug' => $faker->boolean() ? str_slug($categoryName) : null,
            'field' => $faker->boolean(5) ? str_slug($faker->sentence(rand(1, 4))) : null,
            'url' => $faker->url,
            'active' => $faker->boolean(70),
            'last_crawled_products_count' => $faker->boolean() ? $faker->numberBetween() : null,
        ]);

        $resultWebCategory = WebCategory::findOrFail($webCategory->getKey());
        foreach ($webCategory->getFillable() as $fillable) {
            $this->assertTrue($webCategory->$fillable === $resultWebCategory->$fillable, "Fillable {$fillable} expected to be {$webCategory->$fillable} but got {$resultWebCategory->$fillable}");
        }
    }

    /**
     * Test deleting an existing model
     * @return void
     */
    public function testDelete()
    {
        $webCategory = factory(WebCategory::class)->create([
            'deleted_at' => null,
        ]);
        $webCategory->delete();
        $this->assertTrue(WebCategory::count() === 0);
    }

    /**
     * Test deleting multiple existing model
     * @return mixed
     */
    public function testDeleteMultiple()
    {
        $numberOfWebCategoriesToCreate = rand(1, 10);
        $webCategories = factory(WebCategory::class, $numberOfWebCategoriesToCreate)->create([
            'deleted_at' => null,
        ]);
        WebCategory::destroy($webCategories->pluck('id')->toArray());
        $this->assertTrue(WebCategory::count() === 0);
    }
}
