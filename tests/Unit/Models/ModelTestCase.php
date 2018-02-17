<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 11/02/2018
 * Time: 12:45 AM
 */

namespace Tests\Unit\Models;


use Tests\TestCase;

abstract class ModelTestCase extends TestCase
{
    /**
     * Test finding model by ID
     * @return void
     */
    abstract public function testFind();

    /**
     * Test finding model by ID and throw exception if not found
     * @return void
     */
    abstract public function testFindOrFail();

    /**
     * Test fetching all models
     * @return void
     */
    abstract public function testAll();

    /**
     * Test single fetch result's fillable attributes
     * @return void
     */
    abstract public function testSingleFillables();

    /**
     * Test all fetch results' fillable attributes
     * @return void
     */
    abstract public function testAllFillables();

    /**
     * Test single fetch result's hidden attributes
     * @return mixed
     */
    abstract public function testSingleHiddens();

    /**
     * Test all fetch results' hidden attributes
     * @return void
     */
    abstract public function testAllHiddens();

    /**
     * Test single fetch result's appended attributes
     * @return void
     */
    abstract public function testSingleAppends();

    /**
     * Test all fetch results' appended attributes
     * @return void
     */
    abstract public function testAllAppends();

    /**
     * Test storing a new model
     * @return void
     */
    abstract public function testStore();

    /**
     * Test updating an existing model
     * @return void
     */
    abstract public function testUpdate();

    /**
     * Test deleting an existing model
     * @return void
     */
    abstract public function testDelete();

    /**
     * Test deleting multiple existing model
     * @return mixed
     */
    abstract public function testDeleteMultiple();
}