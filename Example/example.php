<?php
/**
 * Created by PhpStorm.
 * User: minus
 * Date: 5/10/15
 * Time: 10:28 PM
 */

include dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src/ArrayPrettyPrint.php';
$data = unserialize(file_get_contents("data.txt"));


$instance = ArrayPrettyPrint::factory($data);
$output = $instance->prettify()->asHTML(true, true, true);

echo PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL;
echo $output;
echo PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL;

exit;


$msgs = array(
    "This CSS is set by default (if flag was provided and set to true).",
    $instance->getCSS() . "\n\n",
    "Use ->setCss to override. style tag can also be emitted with a flag",
    "In order to generate the list, a simple 'echo \$instance->prettify()->asHTML(true, true);' will do the trick.",
    "\t\tfirst true is to include css, 2ed it to include toggle button. See JS folder for jquery usage.",
    "The generated list is as follows (will also be saved into index.html):\n",
);

echo implode("\n", $msgs) . "\n\n";
$output = $instance->prettify()->asHTML(true, true);
echo $output . "\n\n";

$f = __DIR__ . DIRECTORY_SEPARATOR . 'HTML' . DIRECTORY_SEPARATOR . 'index.html';
$h = fopen($f, "w+");
fwrite($h, $output);
fclose($h);

echo "Content was saved into index.html\n\n";