<?php
include("constants.php");
//////// Do not Edit below /////////
try {
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS);
 $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$db->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
	$db = new PDO('mysql:host='.DB_HOST,DB_USER,DB_PASS);
	try { $db->exec("CREATE DATABASE ".DB_NAME); } catch(PDOException $e) { die($e->getMessage());}
}

include('functions.php');
?>