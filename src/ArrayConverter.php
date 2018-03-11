<?php
namespace Lib\ArrayGetAndMap;

class ArrayConverter
{
	/**
	 * Receives an object and returns an associative array with given object's public properties.
	 * Works recursively and can return a multidimensional array.
	 *
	 * @author nikola.tsenov
	 *
	 * @param object $object
	 * @param string $emptyValueDefault - return value if property value is empty
	 * @param bool $includeZero - determines whether to include int(0) in response or to replace it with $emptyValueDefault
	 * @return string|array
	 */
	public static function objectToArray(
		$object,
		$emptyValueDefault = '',
		$includeZero = false
	) {
		$outputArray = [];
	
		if (! empty($object)) {
			$arrayObject = is_object($object) ? get_object_vars($object) : (array) $object;
	
			foreach ($arrayObject as $key => $value) {
				$default = ! $includeZero ? empty($value) : (empty($value) && $value !== 0);
				if (
					is_array($value)
					|| is_object($value)
				) {
					$outputArray[$key] = $default ? $emptyValueDefault : self::objectToArray($value, $emptyValueDefault, $includeZero);
				} else {
					$outputArray[$key] = $default ? $emptyValueDefault : $value;
				}
			}
		}
	
		return $outputArray;
	}
}
