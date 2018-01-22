<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 20/01/2018
 * Time: 2:34 AM
 */

namespace OzSpy\Traits\Responses;

use Illuminate\Database\Eloquent\Model as Builder;

/**
 * Trait Pageable
 * @package OzSpy\Traits\Responses
 */
trait Pageable
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @var int
     */
    protected $total;

    /**
     * @var int
     */
    protected $offset = 0;

    /**
     * @var int
     */
    protected $length = 25;

    /**
     * @var array
     */
    protected $order;

    /**
     * @param array $data
     */
    protected function pageableSet(array $data)
    {
        foreach ($data as $key => $value) {
            if (isset($this->$key)) {
                $this->$key = $value;
            }
        }

        if (is_null($this->offset) || $this->offset < 0) {
            $this->offset = 0;
        }

        if (is_null($this->length) || $this->length < 0 || $this->length > 100) {
            $this->length = 100;
        }
    }

    /**
     * @return array
     */
    protected function pageableComposer()
    {
        $data = [
            'data' => $this->data,
            'total' => $this->total,
            'offset' => $this->offset,
            'length' => $this->length,
            'count' => count($this->data),
        ];
        return $data;
    }

    /**
     * @param Builder $builder
     */
    protected function pageablePrepare(Builder &$builder)
    {
        //count before skip
        $this->total = $builder->count();

        $this->__skip($builder);
        $this->__take($builder);
        $this->__order($builder);
    }

    /**
     * @param $builder
     */
    private function __skip(&$builder)
    {
        if (!is_null($this->offset) && $this->offset > 0) {
            $builder = $builder->skip($this->offset);
        }
    }

    /**
     * @param $builder
     */
    private function __take(&$builder)
    {
        if (!is_null($this->length) && $this->length > 0) {
            $builder = $builder->take($this->length);
        }
    }

    /**
     * @param $builder
     */
    private function __order(&$builder)
    {
        if (!is_null($this->order)) {
            if (!is_array($this->order)) {
                /*single column ordering by asc*/
            } else {
                if (is_array(array_first($this->order))) {
                    /*multiple columns ordering*/
                    foreach ($this->order as $order) {
                        if (count($order) == 2) {
                            list($column, $direction) = $order;
                            switch ($direction) {
                                case 'desc':
                                    $builder = $builder->orderByDesc($column);
                                    break;
                                default:
                                    $builder = $builder->orderBy($column);
                            }
                        }
                    }
                } else {
                    if (count($this->order) == 2) {
                        list($column, $direction) = $this->order;
                        switch ($direction) {
                            case 'desc':
                                $builder = $builder->orderByDesc($column);
                                break;
                            default:
                                $builder = $builder->orderBy($column);
                        }
                    }
                }
            }
        }
    }

}