<?php
namespace Lib\ArrayGetAndMap;

trait InputArrayTrait
{
	//given array
	protected $inputArray;
	
	/**
	 * @author nikola.tsenov
	 *
	 * @param array $array
	 * @throws ArrayMapException
	 */
	protected function setInputArray($array)
	{
		if (! is_array($array)) {
			throw new ArrayMapException('You must pass array to ArrayInput object!');
		}
	
		$this->inputArray = $array;
	}
}
