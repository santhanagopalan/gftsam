<?php

#If required do run  the script tool/setup.sh

if(!isset($log)){
# Should log to the same directory as this file
	require_once __DIR__ . '/klogger/KLogger.php';

	$log   = KLogger::instance(dirname(__FILE__)."/../log/sam", KLogger::DEBUG);
}

?>
