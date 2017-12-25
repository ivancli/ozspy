<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 25/12/2017
 * Time: 3:50 PM
 */

namespace OzSpy\Traits\Commands;


trait Optionable
{
    protected $params = [];

    protected $options = [];

    protected $optionStr = null;

    /**
     * transform params to an array of options
     * @param array $params
     * @return Optionable
     */
    protected function format(array $params = [])
    {
        if (empty($params)) {
            $params = $this->params;
        }

        foreach ($params as $key => $value) {
            $this->options[] = "--{$key}=$value";
        }

        return $this;
    }

    /**
     * join options with space to be a string
     * @param array $options
     * @return Optionable
     */
    protected function toString($options = [])
    {
        if (empty($options)) {
            $options = $this->options;
        }

        $this->optionStr = implode(' ', $options);

        return $this;
    }

    /**
     * @return array
     */
    protected function getParams()
    {
        return $this->params;
    }

    /**
     * @return array
     */
    protected function getOptions()
    {
        return $this->options;
    }

    /**
     * @return string
     */
    protected function getOptionsStr()
    {
        return $this->optionStr;
    }
}