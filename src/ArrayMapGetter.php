<?php
namespace Lib\ArrayGetAndMap;

use Lib\ArrayMap\Exception\ArrayMapException;

class ArrayMapGetter extends ArrayInput
{
	//default return value
	public $default;
	
	//searched value
	private $result = null;
	
	/**
	 * @author nikola.tsenov
	 * 
	 * @param array $array
	 * @param unknown $defaultValue
	 * @throws ArrayMapException
	 */
	public function __construct($array, $defaultValue = false)
	{
		parent::__construct($array);
		
		$this->default = $defaultValue;
	}
	
	/**
	 * Gets private $result
	 * 
	 * @author nikola.tsenov
	 * 
	 * @return unknown
	 */
	public function getResult()
	{
		return $this->result;
	}
	
	/**
	 * Sets private $result
	 * 
	 * @author nikola.tsenov
	 */
	public function setResult($value)
	{
		$this->result = $value;
	}
	
	/**
	 * Returns a key's value by mapping it to a key-value pair from the same level in multidimensional array.
	 * If no matches returns $defaultValue.
	 * 
	 * @author nikola.tsenov
	 * 
	 * @param array $haystack
	 * @param int|string $mapKey
	 * @param unknown $mapValue
	 * @param int|string $searchedKey
	 * @param unknown $defaultValue
	 * @return unknown
	 */
	public static function getAssocValueByMapping($haystack, $mapKey, $mapValue, $searchedKey, $defaultValue = false)
	{
		$arrayMapGetter = new static($haystack, $defaultValue);
		
		return self::getAssocValueByMappingRecursion($arrayMapGetter, $arrayMapGetter->array, $mapKey, $mapValue, $searchedKey);
	}
	
	/**
	 * Gets a key's value by mapping it to a key-value pair from the same level in multidimensional array.
	 * 
	 * @author nikola.tsenov
	 * 
	 * @param ArrayMapHelper $arrayMapHelper
	 * @param array $haystack
	 * @param int|string $mapKey
	 * @param unknown $mapValue
	 * @param int|string $searchedKey
	 * @return unknown
	 */
	protected static function getAssocValueByMappingRecursion(ArrayMapGetter $arrayMapGetter, $haystack, $mapKey, $mapValue, $searchedKey)
	{
		if (isset($haystack[$mapKey]) && $haystack[$mapKey] == $mapValue && array_key_exists($searchedKey, $haystack)) {
			$arrayMapGetter->setResult($haystack[$searchedKey]);
		}
	
		foreach ($haystack AS $key => $value) {
			if (! is_null($arrayMapGetter->getResult())) {
				break;
			}
			if (is_array($value)) {
				self::getAssocValueByMappingRecursion($arrayMapGetter, $value, $mapKey, $mapValue, $searchedKey);
			}
		}
	
		return $arrayMapGetter->getResult() ?? $arrayMapGetter->default;
	}
	
	/**
	 * Returns array of searched keys' values by mapping them to key-value pairs from the same level in multidimensional array.
	 * If no matches returns $defaultValue.
	 *
	 * @author nikola.tsenov
	 *
	 * @param array $haystack
	 * @param array $mapArray
	 * @param array $searchedKeysArray
	 * @param bool $assoc - result will be assoc array with $searchedKeysArray as keys
	 * @param unknown $defaultValue
	 * @return unknown $result
	 */
	public static function getAssocValuesByMultiMapping($haystack, array $mapArray, array $searchedKeysArray, $assoc = false, $defaultValue = false)
	{
		$arrayMapGetter = new static($haystack, $defaultValue);
	
		$result = self::getAssocValuesByMultiMappingRecursion($arrayMapGetter, $arrayMapGetter->array, $mapArray, $searchedKeysArray);
		
		if (! $assoc && ! empty($result) && is_array($result)) {
			return array_values($result);
		}
		
		return $result;
	}
	
	/**
	 * Gets array of searched keys values by mapping them to key-value pairs from the same level in multidimensional array.
	 *
	 * @author nikola.tsenov
	 *
	 * @param array $haystack
	 * @param array $mapArray
	 * @param array $searchedKeysArray
	 * @param unknown $defaultValue
	 * @return unknown
	 */
	protected static function getAssocValuesByMultiMappingRecursion(ArrayMapGetter $arrayMapGetter, $haystack, $mapArray, $searchedKeysArray)
	{
		$bottom = true;
		foreach ($mapArray AS $mapKey => $mapValue) {
			if (! isset($haystack[$mapKey]) || $haystack[$mapKey] != $mapValue) {
				$bottom = false;
				break;
			}
		}
		
		if ($bottom) {
			$foundArray = [];
			foreach ($searchedKeysArray AS $foundKey) {
				if (array_key_exists($foundKey, $haystack)) {
					$foundArray[$foundKey] = $haystack[$foundKey];
				}
			}
			if (! empty($foundArray)) {
				$arrayMapGetter->setResult($foundArray);
			}
		}
	
		foreach ($haystack AS $key => $value) {
			if (! is_null($arrayMapGetter->getResult())) {
				break;
			}
			if (is_array($value)) {
				self::getAssocValuesByMultiMappingRecursion($arrayMapGetter, $value, $mapArray, $searchedKeysArray);
			}
		}
	
		return $arrayMapGetter->getResult() ?? $arrayMapGetter->default;
	}
	
	/**
	 * 
	 * @param array $haystack
	 * @param array $mapArray
	 * @param string $mapType ('keys' if $mapArray consists of keys, 'values' if $mapArray consists of values, 'pairs' if $mapArray is assoc)
	 * @param string $excludeMappers (if true $mapArray excluded from result)
	 * @param string $defaultValue
	 */
	public static function getAssocPairsByMapping($haystack, array $mapArray, $mapType, $excludeMappers = true, $defaultValue = false)
	{
		$arrayMapHelper = new static($haystack, $defaultValue);
		
		$result = $arrayMapHelper->default;
		switch ($mapType) {
			case 'keys' :
				$result = self::getAssocPairsByMappingKeysRecursion($arrayMapHelper, $haystack, $mapArray);
				break;
			case 'values' :
				$result = self::getAssocPairsByMappingValuesRecursion($arrayMapHelper, $haystack, $mapArray);
				break;
			case 'pairs' :
				$result = self::getAssocPairsByMappingPairsRecursion($arrayMapHelper, $haystack, $mapArray);
				break;
			default :
				throw new ArrayMapException('"' . $mapType . '" is not a valid map type. Use "keys", "values" or "pairs".');
				break;
		}
		
		if ($excludeMappers && $result != $arrayMapHelper->default) {
			if ($mapType != 'keys') {
				$result = array_diff($result, $mapArray);
			} else {
				foreach ($mapArray AS $key => $value) {
					unset($result[$key]);
				}
			}
		}
		
		return $result;
	}
	
	protected static function getAssocPairsByMappingKeysRecursion(ArrayMapHelper $arrayMapHelper, $haystack, $mapArray)
	{
		
	}
	
	protected static function getAssocPairsByMappingValuesRecursion(ArrayMapHelper $arrayMapHelper, $haystack, $mapArray)
	{
	
	}
	
	protected static function getAssocPairsByMappingPairsRecursion(ArrayMapHelper $arrayMapHelper, $haystack, $mapArray)
	{
	
	}
}
