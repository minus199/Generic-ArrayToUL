<?php
/**
 * Created by PhpStorm.
 * User: minus
 * Date: 5/11/15
 * Time: 8:52 PM
 */

$ulElement = file_get_contents('Example/HTML/index.html');

$dom = new DOMDocument('', 'utf-8');
$html = $dom->createElement('html');

/* Start head */
$html
    ->appendChild($dom->createElement('head'))
    ->appendChild($dom->createElement('title', 'output html'))
    ->appendChild($dom->createElement('script')->setAttribute('src', 'http://code.jquery.com/jquery-2.0.3.js'));

/* End head */

/* Start Body */
$body = $dom->createElement('body');
$dom->appendChild('head')



