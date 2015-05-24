<?php
/**
 * Created by PhpStorm.
 * User: minus
 * Date: 5/10/15
 * Time: 10:28 PM
 */

/* Include main file */
$baseDir = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR;
$fileName = $baseDir . "src" . DIRECTORY_SEPARATOR . "ArrayPrettyPrint.php";
include $fileName;


//$path = dirname(__DIR__) . DIRECTORY_SEPARATOR . "Resources" . DIRECTORY_SEPARATOR . "JS.min";
//
//$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
//foreach ($iterator as $file){
//    /* @var $file SplFileInfo */
//    if ($file->getFilename() == "." || $file->getFilename() == ".."){
//        continue;
//    }
//
//    $a = `date -r $file`;
//    echo $a . PHP_EOL;
//
//    $output = `./vendor/UglifyJS2/bin/uglifyjs --source-map Resources/JS.min/src.map --compress --mangle -- {$file}`;
//
//    $handle = fopen($file, "w+");
//    fwrite($handle, $output);
//    fclose($handle);
//
//    $b = `date -r $file`;
//    echo $b . PHP_EOL;
//
//    var_dump($a < $b);
//    echo str_repeat(".", 50) . PHP_EOL. PHP_EOL;
//}
//
//exit;


/* Debugging!! */
$fileName = $baseDir . "Resources" . DIRECTORY_SEPARATOR . "data.txt";
if (!file_exists($fileName))
    throw new \Exception("\n\nPlease include serialized array in data.txt inside folder " . $baseDir . "Resources\n\n");
$data = unserialize(file_get_contents($fileName));
/* Debugging!! */

/* Instantiate prettier */
$instance = \MiNuS199\ArrayPrettyPrint::factory($data);
$output = $instance->prettify()->asHTML(true, true, true, true, false);

/* Save into html flie */
$f = $baseDir . "Example" . DIRECTORY_SEPARATOR . "HTML" . DIRECTORY_SEPARATOR . "index.html";
$h = fopen($f, "w+");
if (ftruncate($h, 0))
    fwrite($h, $output);
fclose($h);

echo str_repeat("=", 100) . "\nContent was saved into $f\n\n";