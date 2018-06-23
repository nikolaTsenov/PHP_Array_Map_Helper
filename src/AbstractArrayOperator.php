<?php
namespace Lib\ArrayGetAndMap;

abstract class AbstractArrayOperator implements ArrayOperatorInterface
{
    use InputArrayTrait, OutputTrait;

    /**
     *
     * @author nikola.tsenov
     *
     * @param array $array
     * @param unknown $defaultValue
     * @throws ArrayMapException
     */
    public function __construct($array, $defaultValue = false)
    {
        $this->setInputArray($array);
        $this->setDefaultValue($defaultValue);
    }
}
