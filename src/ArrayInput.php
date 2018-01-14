<?php
namespace Lib\ArrayGetAndMap;

abstract class ArrayInput
{
	//given array
	protected $array;
	
	/**
	 * @author nikola.tsenov
	 *
	 * @param array $array
	 * @throws ArrayMapException
	 */
	public function __construct($array)
	{
		if (! is_array($array)) {
			throw new ArrayMapException('You must pass array to ArrayInput object!');
		}
	
		$this->array = $array;
	}
	
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
	public static function convertObjectToArray($object, $emptyValueDefault = '', $includeZero = false)
	{
		$outputArray = [];
		
		if (! empty($object)) {
			$arrayObject = is_object($object) ? get_object_vars($object) : (array) $object;

			foreach ($arrayObject as $key => $value) {
				$default = ! $includeZero ? empty($value) : (empty($value) && $value !== 0);
				if (is_array($value) || is_object($value)) {
					$outputArray[$key] = $default ? $emptyValueDefault : self::convertObjectToArray($value, $emptyValueDefault, $includeZero);
				} else {
					$outputArray[$key] = $default ? $emptyValueDefault : $value;
				}
			}
		}
		
		return $outputArray;
	}
}
