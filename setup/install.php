<?php
if(file_exists('../includes/db.php'))
{
include('../includes/db.php');
	$sql = file_get_contents('lurch.sql');
	try { $db->exec($sql);}
	catch(PDOException $e) { die($e->getMessage());}
}
else
{
	die("ERROR: FILE ./includes/db.php DOES NOT EXIST! \n");
}
