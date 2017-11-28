<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 21/11/2017
 * Time: 6:38 PM
 */

namespace OzSpy\Contracts\Models\Base;


use Illuminate\Database\Eloquent\Model;

class BaseContract
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * get all models
     * @param bool $trashed
     * @return \Illuminate\Database\Eloquent\Collection|Model[]
     */
    public function all($trashed = false)
    {
        if ($trashed === true) {
            return $this->model->withTrashed()->get();
        } else {
            return $this->model->all();
        }
    }

    /**
     * get model by id
     * @param $id
     * @param bool $throw
     * @return Model|null
     */
    public function get($id, $throw = true)
    {
        if ($throw === true) {
            return $this->model->findOrFail($id);
        } else {
            return $this->model->find($id);
        }
    }

    /**
     * create a new model
     * @param array $data
     * @return Model
     */
    public function store(array $data)
    {
        $data = $this->__getData($data);
        return $this->model->create($data);
    }

    /**
     * update an existing model
     * @param Model $model
     * @param array $data
     * @return bool
     */
    public function update(Model $model, array $data)
    {
        $data = $this->__getData($data);
        return $model->update($data);
    }

    /**
     * delete a model
     * @param Model $model
     * @param bool $force
     * @return bool
     */
    public function delete(Model $model, $force = false)
    {
        if ($force === true) {
            return $model->forceDelete();
        } else {
            return $model->delete();
        }
    }

    /**
     * restore a model
     * @param Model $model
     * @return bool
     */
    public function restore(Model $model)
    {
        return $model->restore();
    }

    private function __getData(array $data)
    {
        return array_only($data, $this->model->getFillable());
    }
}