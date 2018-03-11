<?php
namespace Lib\ArrayGetAndMap;

use Lib\ArrayMap\Exception\ArrayMapException;

class ArrayMapGetter extends AbstractArrayOperator
{
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
	public static function getAssocValueByMapping(
		$haystack,
		$mapKey,
		$mapValue,
		$searchedKey,
		$defaultValue = false
	) {
		$arrayMapGetter = new static($haystack, $defaultValue);
		
		return self::getAssocValueByMappingRecursion($arrayMapGetter, $arrayMapGetter->inputArray, $mapKey, $mapValue, $searchedKey);
	}
	
	/**
	 * Gets a key's value by mapping it to a key-value pair from the same level in multidimensional array.
	 * 
	 * @author nikola.tsenov
	 * 
	 * @param ArrayOperatorInterface $arrayMapGetter
	 * @param array $haystack
	 * @param int|string $mapKey
	 * @param unknown $mapValue
	 * @param int|string $searchedKey
	 * @return unknown
	 */
	protected static function getAssocValueByMappingRecursion(
		ArrayOperatorInterface $arrayMapGetter,
		$haystack,
		$mapKey,
		$mapValue,
		$searchedKey
	) {
		if (
			isset($haystack[$mapKey])
			&& $haystack[$mapKey] == $mapValue
			&& array_key_exists($searchedKey, $haystack)
		) {
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
	 * @return array|default value
	 */
	public static function getAssocValuesByMultiMapping(
		$haystack,
		array $mapArray,
		array $searchedKeysArray,
		$assoc = false,
		$defaultValue = false
	) {
		$arrayMapGetter = new static($haystack, $defaultValue);
	
		$result = self::getAssocValuesByMultiMappingRecursion($arrayMapGetter, $arrayMapGetter->inputArray, $mapArray, $searchedKeysArray);
		
		if (
			! $assoc
			&& ! empty($result) && is_array($result)
		) {
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
	 * @return array|default value
	 */
	protected static function getAssocValuesByMultiMappingRecursion(
		ArrayOperatorInterface $arrayMapGetter,
		$haystack,
		$mapArray,
		$searchedKeysArray
	) {
		$bottom = true;
		foreach ($mapArray AS $mapKey => $mapValue) {
			if (
				! isset($haystack[$mapKey])
				|| $haystack[$mapKey] != $mapValue
			) {
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
	 * Gets array by mapping it to pairs, values or keys from the same level in multidimensional array.
	 * 
	 * @author nikola.tsenov
	 * 
	 * @param array $haystack
	 * @param array $mapArray
	 * @param string $mapType ('keys' if $mapArray consists of keys, 'values' if $mapArray consists of values, 'pairs' if $mapArray is assoc)
	 * @param string $excludeMappers (if true $mapArray excluded from result)
	 * @param string $defaultValue
	 * @return array|default value
	 */
	public static function getAssocPairsByMapping(
		$haystack,
		array $mapArray,
		$mapType,
		$excludeMappers = true,
		$defaultValue = false
	) {
		$arrayMapGetter = new static($haystack, $defaultValue);
		
		$result = $arrayMapGetter->default;
		switch ($mapType) {
			case 'keys' :
				$result = self::getAssocPairsByMappingKeysRecursion($arrayMapGetter, $haystack, $mapArray);
				break;
			case 'values' :
				$result = self::getAssocPairsByMappingValuesRecursion($arrayMapGetter, $haystack, $mapArray);
				break;
			case 'pairs' :
				$result = self::getAssocPairsByMappingPairsRecursion($arrayMapGetter, $haystack, $mapArray);
				break;
			default :
				throw new ArrayMapException('"' . $mapType . '" is not a valid map type. Use "keys", "values" or "pairs".');
				break;
		}
		
		if (
			$excludeMappers
			&& $result != $arrayMapGetter->default
		) {
			switch ($mapType) {
				case 'keys' :
					foreach ($mapArray AS $keyValue) {
						unset($result[$keyValue]);
					}
					break;
				case 'values' :
					foreach ($result AS $key => $value) {
						if (in_array($value, $mapArray)) {
							unset($result[$key]);
						}
					}
					break;
				case 'pairs' :
					foreach ($mapArray AS $key => $value) {
						unset($result[$key]);
					}
					break;
			}
		}
		
		return $result;
	}
	
	/**
	 * Gets array by mapping it to keys from the same level in multidimensional array.
	 *
	 * @author nikola.tsenov
	 *
	 * @param ArrayOperatorInterface $arrayMapGetter
	 * @param array $haystack
	 * @param array $keysArray
	 * @return array|default value
	 */
	protected static function getAssocPairsByMappingKeysRecursion(
		ArrayOperatorInterface $arrayMapGetter,
		$haystack,
		$keysArray
	) {
		$bottom = true;
		foreach ($keysArray AS $keyValue) {
			if (! isset($haystack[$keyValue])) {
				$bottom = false;
				break;
			}
		}
		
		if ($bottom) {
			$arrayMapGetter->setResult($haystack);
		}
		
		foreach ($haystack AS $key => $value) {
			if (! is_null($arrayMapGetter->getResult())) {
				break;
			}
			if (is_array($value)) {
				self::getAssocPairsByMappingKeysRecursion($arrayMapGetter, $value, $keysArray);
			}
		}
		
		return $arrayMapGetter->getResult() ?? $arrayMapGetter->default;
	}
	
	/**
	 * Gets array by mapping it to values from the same level in multidimensional array.
	 *
	 * @author nikola.tsenov
	 *
	 * @param ArrayOperatorInterface $arrayMapGetter
	 * @param array $haystack
	 * @param array $valuesArray
	 * @return array|default value
	 */
	protected static function getAssocPairsByMappingValuesRecursion(
		ArrayOperatorInterface $arrayMapGetter,
		$haystack,
		$valuesArray
	) {
		$bottom = false;
		$temp = $valuesArray;
		foreach ($temp AS $tempKey => $tempValue) {
			if (in_array($tempValue, $haystack)) {
				unset($temp[$tempKey]);
			}
		}
		if (empty($temp)) {
			$bottom = true;
		}
		
		if ($bottom) {
			$arrayMapGetter->setResult($haystack);
		}
		
		foreach ($haystack AS $key => $value) {
			if (! is_null($arrayMapGetter->getResult())) {
				break;
			}
			if (is_array($value)) {
				self::getAssocPairsByMappingValuesRecursion($arrayMapGetter, $value, $valuesArray);
			}
		}
		
		return $arrayMapGetter->getResult() ?? $arrayMapGetter->default;
	}
	
	/**
	 * Gets array by mapping it to pairs from the same level in multidimensional array.
	 *
	 * @author nikola.tsenov
	 *
	 * @param ArrayOperatorInterface $arrayMapGetter
	 * @param array $haystack
	 * @param array $pairsArray
	 * @return array|default value
	 */
	protected static function getAssocPairsByMappingPairsRecursion(
		ArrayOperatorInterface $arrayMapGetter,
		$haystack,
		$pairsArray
	) {
		$bottom = false;
		$temp = $pairsArray;
		foreach ($temp AS $tempKey => $tempValue) {
			if (
				isset($haystack[$tempKey])
				&& in_array($tempValue, $haystack)
			) {
				unset($temp[$tempKey]);
			}
		}
		if (empty($temp)) {
			$bottom = true;
		}
		
		if ($bottom) {
			$arrayMapGetter->setResult($haystack);
		}
		
		foreach ($haystack AS $key => $value) {
			if (! is_null($arrayMapGetter->getResult())) {
				break;
			}
			if (is_array($value)) {
				self::getAssocPairsByMappingPairsRecursion($arrayMapGetter, $value, $pairsArray);
			}
		}
		
		return $arrayMapGetter->getResult() ?? $arrayMapGetter->default;
	}
}
