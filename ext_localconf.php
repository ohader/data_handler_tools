<?php
if (!defined('TYPO3_MODE')) {
	die ("Access denied.");
}

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['GLOBAL']['cliKeys']['data_handler_tools'] = array(
	'EXT:' . $_EXTKEY . '/Scripts/Dispatcher.php',
	'_CLI_data_handler_tools',
);
?>