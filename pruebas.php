<?php
header('Content-Type: text/plain');
use HynoTech\UsosGenerales\FetchCurl;

require __DIR__.'/vendor/autoload.php';

$a = new FetchCurl();

//echo "<textarea>";
//print_r($a->getContent('https://www.dropbox.com/sh/8ifs95x8qgcaf71/AAAUdBHtQXdzkZRYpPsE1x3SMa?dl=0'));
echo $a->getContent('https://www.dropbox.com/sh/8ifs95x8qgcaf71/AAAUdBHtQXdzkZRYpPsE1x3SMa?dl=0');
//echo "</textarea>";
