<?php
namespace Lib\ArrayGetAndMap;

trait OutputTrait
{
    // default return value
    protected $default;
    
    // searched value
    private $result = null;

    /**
     * Sets a default return value
     *
     * @author nikola.tsenov
     *
     * @param unknown $default
     */
    protected function setDefaultValue($default)
    {
        $this->default = $default;
    }

    /**
     * Gets private $result
     *
     * @author nikola.tsenov
     *
     * @return unknown
     */
    protected function getResult()
    {
        return $this->result;
    }

    /**
     * Sets private $result
     *
     * @author nikola.tsenov
     */
    protected function setResult($value)
    {
        $this->result = $value;
    }
}
