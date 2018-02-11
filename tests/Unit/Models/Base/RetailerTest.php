<?php

namespace Tests\Unit\Models\Base;

use OzSpy\Models\Base\Retailer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Unit\Models\ModelTestCase;

class RetailerTest extends ModelTestCase
{
    use RefreshDatabase;

    /**
     * Test finding model by ID
     * @return void
     */
    public function testFind()
    {
        $retailer = factory(Retailer::class)->create([
            'deleted_at' => null,
        ]);
        try {
            $resultRetailer = Retailer::find($retailer->getKey());
            /*retailer found*/
            $this->assertTrue(!is_null($resultRetailer));
        } catch (\Exception $e) {
            /*shouldn't throw exception as retailer exists*/
            $this->assertFalse(true);
        }
    }

    /**
     * Test finding model by ID and throw exception if not found
     * @return void
     */
    public function testFindOrFail()
    {
        $retailer = factory(Retailer::class)->create([
            'deleted_at' => null,
        ]);
        try {
            $resultRetailerSuccess = Retailer::findOrFail($retailer->getKey());
            /*retailer found*/
            $this->assertTrue(!is_null($resultRetailerSuccess));
        } catch (\Exception $e) {
            /*shouldn't throw exception as retailer exists*/
            $this->assertFalse(true);
        }

        try {
            $resultRetailerFailed = Retailer::findOrFail($retailer->getKey() + 1);
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
        $numberOfRetailersToCreate = rand(1, 100);

        $retailers = factory(Retailer::class, $numberOfRetailersToCreate)->create();

        /*make sure deleted retailers are not counted*/
        $retailers = $retailers->filter(function (Retailer $retailer) {
            return is_null($retailer->deleted_at);
        });

        $fetchedRetailers = Retailer::all();
        $this->assertTrue($retailers->count() === $fetchedRetailers->count());
        $fetchedRetailers->each(function ($fetchRetailer) use ($retailers) {
            $matchedRetailers = $retailers->filter(function ($retailer) use ($fetchRetailer) {
                return $retailer->getKey() === $fetchRetailer->getKey();
            });
            $this->assertTrue($matchedRetailers->count() === 1, "retailer with ID [{$fetchRetailer->getKey()}] cannot be found.");
        });
    }

    /**
     * Test single fetch result's fillable attributes
     * @return void
     */
    public function testSingleFillables()
    {
        $retailer = factory(Retailer::class)->create([
            'deleted_at' => null,
        ]);

        $fetchedRetailer = Retailer::findOrFail($retailer->getKey());
        $fillableAttributes = $fetchedRetailer->getFillable();
        if (count($fillableAttributes) > 0) {
            foreach ($fillableAttributes as $fillableAttribute) {
                $this->assertTrue(isset($retailer->$fillableAttribute), "Attribute [{$fillableAttribute}] is missing in retailer {$retailer->getKey()}");
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
        $numberOfRetailersToCreate = rand(1, 10);
        $retailers = factory(Retailer::class, $numberOfRetailersToCreate)->create();
        $fetchedRetailers = Retailer::all();
        if ($fetchedRetailers->count() > 0) {
            $fetchedRetailers->each(function (Retailer $retailer) {
                $retailerArray = $retailer->toArray();
                $fillableAttributes = $retailer->getFillable();
                if (count($fillableAttributes) > 0) {
                    foreach ($fillableAttributes as $fillableAttribute) {
                        $this->assertTrue(isset($retailer->$fillableAttribute), "Attribute [{$fillableAttribute}] is missing in retailer {$retailer->getKey()}");
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
        $retailer = factory(Retailer::class)->create([
            'deleted_at' => null,
        ]);

        $fetchedRetailer = Retailer::findOrFail($retailer->getKey());
        $retailerArray = $fetchedRetailer->toArray();
        $hiddenAttributes = $fetchedRetailer->getHidden();
        if (count($hiddenAttributes) > 0) {
            foreach ($hiddenAttributes as $hiddenAttribute) {
                $this->assertArrayNotHasKey($hiddenAttribute, $retailerArray, "Attribute [{$hiddenAttribute}] appears in retailer {$retailer->getKey()}");
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
        $numberOfRetailersToCreate = rand(1, 10);
        $retailers = factory(Retailer::class, $numberOfRetailersToCreate)->create();
        $fetchedRetailers = Retailer::all();
        if ($fetchedRetailers->count() > 0) {
            $fetchedRetailers->each(function (Retailer $retailer) {
                $retailerArray = $retailer->toArray();
                $hiddenAttributes = $retailer->getHidden();
                if (count($hiddenAttributes) > 0) {
                    foreach ($hiddenAttributes as $hiddenAttribute) {
                        $this->assertArrayNotHasKey($hiddenAttribute, $retailerArray, "Attribute [{$hiddenAttribute}] appears in retailer {$retailer->getKey()}");
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
        $retailer = factory(Retailer::class)->create([
            'deleted_at' => null,
        ]);

        $fetchedRetailer = Retailer::findOrFail($retailer->getKey());
        $retailerArray = $fetchedRetailer->toArray();
        $appendedAttributes = Retailer::getAppends();
        if (count($appendedAttributes) > 0) {
            foreach ($appendedAttributes as $appendedAttribute) {
                $this->assertArrayHasKey($appendedAttribute, $retailerArray, "Attribute [{$appendedAttribute}] is missing in retailer {$retailer->getKey()}");
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
        $numberOfRetailersToCreate = rand(1, 10);
        $retailers = factory(Retailer::class, $numberOfRetailersToCreate)->create();
        $fetchedRetailers = Retailer::all();

        $appendedAttributes = Retailer::getAppends();

        if ($fetchedRetailers->count() > 0 && count($appendedAttributes) > 0) {
            $fetchedRetailers->each(function (Retailer $retailer) use ($appendedAttributes) {
                $retailerArray = $retailer->toArray();
                foreach ($appendedAttributes as $appendedAttribute) {
                    $this->assertArrayHasKey($appendedAttribute, $retailerArray, "Attribute [{$appendedAttribute}] is missing in retailer {$retailer->getKey()}");
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
        $this->assertTrue(true);
        // TODO: Implement testStore() method.
    }

    /**
     * Test updating an existing model
     * @return void
     */
    public function testUpdate()
    {
        $this->assertTrue(true);
        // TODO: Implement testUpdate() method.
    }

    /**
     * Test deleting an existing model
     * @return void
     */
    public function testDelete()
    {
        $this->assertTrue(true);
        // TODO: Implement testDelete() method.
    }
}
