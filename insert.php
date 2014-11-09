<?php

require_once (__DIR__ . "/include.php");

$logHandler = new \lib\logHandler($dbh);

echo $logHandler->parseLog(__DIR__ . "/logs/2014-11-09.txt");

