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
$output = $instance->prettify()->asHTML(true, true, true, false);

/* some tl;dr */
$msgs = array(
    str_repeat("=", 100),
    " * This CSS is set by default (if flag was provided and set to true).",
    $instance->getCSS()->nodeValue . "\n\n",
    " \t~ Use ->setCss to override. style tag can also be emitted with a flag",
    " * In order to generate the list, a simple 'echo \$instance->prettify()->asHTML(true, true);' will do the trick.",
    " \t~ first true is to include css, 2ed it to include toggle button. See JS folder for jquery usage.",
    " * [--CLIPPED--]The generated list is as follows (will also be saved into index.html):\n",
    str_repeat("=", 100) . "\n\n\n"
);

echo implode(PHP_EOL, $msgs);

/* Save into html flie */
$f = $baseDir . "Example" . DIRECTORY_SEPARATOR . "HTML" . DIRECTORY_SEPARATOR . "index.html";
$h = fopen($f, "w+");
if(ftruncate($h, 0))
    fwrite($h, $output);
fclose($h);
echo $output . "\n\n" . str_repeat("=", 100) . "\n";

echo "Content was saved into $fileName\n\n";