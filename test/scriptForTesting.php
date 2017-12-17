<?php
use Lib\ArrayMap\ArrayMapHelper;
use Lib\ArrayMap\Exception\ArrayMapException;

// Autoload all classes:

require_once '../src/ArrayMapHelper.php';
require_once '../src/ArrayMapException.php';

// For errors:
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function dumpAndDieScript()
{
	echo '<pre>';
	array_map(function($x) {
		var_dump($x); 
	}, func_get_args());
	
	echo "<br/><br/><br/> ---!!!TRACE!!!--- <br/><br/><br/>";
	
	var_dump([
		'file' => debug_backtrace()[0]['file'],
		'line' => debug_backtrace()[0]['line']]
	);
	die();
}

try {
	//include example array
	include_once './exampleArray.php';

	$searchValue = ArrayMapHelper::getAssocValueByMapping($array, 'subjectname', 'Law issues', 'tension');
	$searchValue2 = ArrayMapHelper::getAssocValueByMapping($array, 'materialid', 'adsasd-asdasd-asdasd-asdasd', 'materialsubject');
	$searchValue3 = ArrayMapHelper::getAssocValueByMapping($array, 'materialid', 'adsasd-asdasd-asdasd-asdasd', 'materialtype');
	$searchValue4 = ArrayMapHelper::getAssocValueByMapping($array, 0, 1, 1);
	$searchValue5 = ArrayMapHelper::getAssocValueByMapping($array, 'subjectid', 2, 'tension');
	$searchValue6 = ArrayMapHelper::getAssocValueByMapping($array, 'subjectid', 2, 'tension');
	$searchValue7 = ArrayMapHelper::getAssocValueByMapping($array, 0, '', 'materialsubject');
	$searchValue8 = ArrayMapHelper::getAssocValueByMapping($array, 0, '', 'materialsubject', 'default');

	dumpAndDieScript($searchValue, $searchValue2, $searchValue3, $searchValue4, $searchValue5, $searchValue6, $searchValue7, $searchValue8);
} catch(ArrayMapException $ex) {
	$erMsg = $ex->getMessage();
	echo $erMsg;
}