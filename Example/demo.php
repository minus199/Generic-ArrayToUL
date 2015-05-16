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

/* Debugging!! */
    $fileName = $baseDir . "Resources" . DIRECTORY_SEPARATOR . "data.txt";
    if (!file_exists($fileName))
        throw new \Exception("Please include serialized array in data.txt inside folder " . $baseDir . "Resources");
    $data = unserialize(file_get_contents($fileName));
/* Debugging!! */

/* Instantiate prettier */
$instance = ArrayPrettyPrint::factory($data);
$output = $instance->prettify()->asHTML(true, true, true, true);

/* Save into html flie */
$f = $baseDir . "Example" . DIRECTORY_SEPARATOR . "HTML" . DIRECTORY_SEPARATOR . "index.html";
$h = fopen($f, "w+");
if(ftruncate($h, 0))
    fwrite($h, $output);
fclose($h);

echo str_repeat("=", 100) . "\nContent was saved into $f\n\n";