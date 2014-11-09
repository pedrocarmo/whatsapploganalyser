<?php

require_once (__DIR__ . "/config/config.php");
require_once (__DIR__ . "/lib/logHandler.php");
require_once (__DIR__ . "/lib/dbHandler.php");
require_once (__DIR__ . "/lib/logStats.php");

$dbh = new \lib\dbHandler($config['db']['host'],
							$config['db']['database'],
							$config['db']['user'],
							$config['db']['pass']);

