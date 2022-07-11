<?php
define("ABS_PATH",realpath($_SERVER["DOCUMENT_ROOT"]));
define('debug', 'off');
ini_set('error_reporting', E_ALL);
error_reporting(E_ALL);
define('LIVE','local');

switch(LIVE)
{
	case "local":
	define("DB_HOST", 'localhost');
	define("DB_USER", 'root');
	define("DB_PASS", '');
	define("DB_NAME", "lurch");
		break;
}
include('gamecon.php');
?>