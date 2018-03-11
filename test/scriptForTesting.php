<?php
use Lib\ArrayGetAndMap\ArrayConverter;
use Lib\ArrayGetAndMap\ArrayMapGetter;
use Lib\ArrayGetAndMap\Exception\ArrayMapException;

// Autoload all classes:
require_once '../src/OutputTrait.php';
require_once '../src/InputArrayTrait.php';
require_once '../src/AbstractArrayOperator.php';
require_once '../src/ArrayOperatorInterface.php';
require_once '../src/ArrayConverter.php';
require_once '../src/ArrayMapGetter.php';
require_once '../src/ArrayMapException.php';
require_once '../src/TestClass.php';

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
?>
<!DOCTYPE html>
<html>
    <head>
		<style>
			body {
				background-color: lightGrey; /* for eye protection */
			}
		</style>
    </head>
	<body>
		<?php
		try {
			//include example array
			include_once './exampleArray.php';
			
			//getAssocValueByMapping
			$searchValue = ArrayMapGetter::getAssocValueByMapping($array, 'subjectname', 'Law issues', 'tension');
			$searchValue2 = ArrayMapGetter::getAssocValueByMapping($array, 'materialid', 'adsasd-asdasd-asdasd-asdasd', 'materialsubject');
			$searchValue3 = ArrayMapGetter::getAssocValueByMapping($array, 'materialid', 'adsasd-asdasd-asdasd-asdasd', 'materialtype');
			$searchValue4 = ArrayMapGetter::getAssocValueByMapping($array, 0, 1, 1);
			$searchValue5 = ArrayMapGetter::getAssocValueByMapping($array, 'subjectid', 2, 'tension');
			$searchValue6 = ArrayMapGetter::getAssocValueByMapping($array, 'subjectid', 2, 'tension');
			$searchValue7 = ArrayMapGetter::getAssocValueByMapping($array, 0, '', 'materialsubject');
			$searchValue8 = ArrayMapGetter::getAssocValueByMapping($array, 0, '', 'materialsubject', 'default');
			
			//getAssocValuesByMultiMapping
			$searchValue9 = ArrayMapGetter::getAssocValuesByMultiMapping($array, [
				'materialid' => 'kjashfbkasdjbffka-adhksfbdlas-dasfdasf-asdfdfas',
				'materialtype' => 'pp presentation',
			], ['materialsubject']);
			$searchValue10 = ArrayMapGetter::getAssocValuesByMultiMapping($array, [
					'subjectid' => 2,
					'subjectname' => 'New business investments',
			], ['tension', 'materials'], true);
			$searchValue11 = ArrayMapGetter::getAssocValuesByMultiMapping($array, [
					'version' => '2.0',
			], [0]);
			$searchValue12 = ArrayMapGetter::getAssocValuesByMultiMapping($array, [
					'version' => '2.0',
			], [0], false, 'default');
			
			//objectToArray
			$testObject = new TestClass();
			$convertedTestObject1 = ArrayConverter::objectToArray($testObject);
			$convertedTestObject2 = ArrayConverter::objectToArray($testObject, 'default');
			$convertedTestObject3 = ArrayConverter::objectToArray($testObject, 'default', true);
		
			dumpAndDieScript(
				'<br/>----------!!!getAssocValueByMapping!!!----------<br/>',
					$searchValue,
					$searchValue2, 
					$searchValue3, 
					$searchValue4, 
					$searchValue5, 
					$searchValue6, 
					$searchValue7, 
					$searchValue8,
				'<br/>----------!!!getAssocValuesByMultiMapping!!!----------<br/>',
					$searchValue9,
					$searchValue10,
					$searchValue11,
					$searchValue12,
				'<br/>----------!!!convertObjectToArray!!!----------<br/>',
					$convertedTestObject1,
					$convertedTestObject2,
					$convertedTestObject3
			);
		} catch(ArrayMapException $ex) {
			$erMsg = $ex->getMessage();
			echo $erMsg;
		}
		?>
    </body>
</html>