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
	private $result = null;
	
	public function __construct($array, $defaultValue = false)
	{
		if (! is_array($array)) {
			throw new ArrayMapException('You must pass array to ArrayMapHelper object!');
		}
		
		$this->array = $array;
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
		$arrayMapHelper = new static($haystack, $defaultValue);
		
		return self::getAssocValueByMappingRecursion($arrayMapHelper, $arrayMapHelper->array, $mapKey, $mapValue, $searchedKey);
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
	protected static function getAssocValueByMappingRecursion(ArrayMapHelper $arrayMapHelper, $haystack, $mapKey, $mapValue, $searchedKey)
	{
		if (isset($haystack[$mapKey]) && $haystack[$mapKey] == $mapValue && array_key_exists($searchedKey, $haystack)) {
			$arrayMapHelper->setResult($haystack[$searchedKey]);
		}
	
		foreach ($haystack AS $key => $value) {
			if (! is_null($arrayMapHelper->getResult())) {
				break;
			}
			if (is_array($value)) {
				self::getAssocValueByMappingRecursion($arrayMapHelper, $value, $mapKey, $mapValue, $searchedKey);
			}
		}
	
		return $arrayMapHelper->getResult() ?? $arrayMapHelper->default;
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
		$arrayMapHelper = new static($haystack, $defaultValue);
	
		$result = self::getAssocValuesByMultiMappingRecursion($arrayMapHelper, $arrayMapHelper->array, $mapArray, $searchedKeysArray);
		
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
	protected static function getAssocValuesByMultiMappingRecursion(ArrayMapHelper $arrayMapHelper, $haystack, $mapArray, $searchedKeysArray)
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
				$arrayMapHelper->setResult($foundArray);
			}
		}
	
		foreach ($haystack AS $key => $value) {
			if (! is_null($arrayMapHelper->getResult())) {
				break;
			}
			if (is_array($value)) {
				self::getAssocValuesByMultiMappingRecursion($arrayMapHelper, $value, $mapArray, $searchedKeysArray);
			}
		}
	
		return $arrayMapHelper->getResult() ?? $arrayMapHelper->default;
	}
}
