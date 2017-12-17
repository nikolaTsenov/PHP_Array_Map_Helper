<?php
namespace Lib\ArrayMap;

use Lib\ArrayMap\Exception\ArrayMapException;

class ArrayMapHelper
{
	//given array
	public $array;
	
	//default return value
	public $default;
	
	//searched value
	private $searchedValue = null;
	
	public function __construct($array, $defaultValue = false)
	{
		if (! is_array($array)) {
			throw new ArrayMapException('You must pass array to ArrayMapHelper object!');
		}
		
		$this->array = $array;
		$this->default = $defaultValue;
	}
	
	public function getSearchedValue()
	{
		return $this->searchedValue;
	}
	
	public function setSearchedValue($value)
	{
		$this->searchedValue = $value;
	}
	
	public static function getAssocValueByMapping($haystack, $mapKey, $mapValue, $searchedKey, $defaultValue = false)
	{
		$arrayMapHelper = new static($haystack, $defaultValue);
		
		return self::getAssocValueByMappingRecursion($arrayMapHelper, $arrayMapHelper->array, $mapKey, $mapValue, $searchedKey);
	}
	
	protected static function getAssocValueByMappingRecursion(ArrayMapHelper $arrayMapHelper, $haystack, $mapKey, $mapValue, $searchedKey)
	{
		if (isset($haystack[$mapKey]) && $haystack[$mapKey] == $mapValue && array_key_exists($searchedKey, $haystack)) {
			$arrayMapHelper->setSearchedValue($haystack[$searchedKey]);
		}
	
		foreach ($haystack AS $key => $value) {
			if (! is_null($arrayMapHelper->getSearchedValue())) {
				break;
			}
			if (is_array($value)) {
				self::getAssocValueByMappingRecursion($arrayMapHelper, $value, $mapKey, $mapValue, $searchedKey);
			}
		}
	
		return $arrayMapHelper->getSearchedValue() ?? $arrayMapHelper->default;
	}
}
