<?php
require('includes/db.php');
$cit = new base($db,0);
$base_citizens = 16;
$sql = "TRUNCATE citizens; TRUNCATE genetics;";
$sql2 = "TRUNCATE dead_dna; TRUNCATE gravestones;";
$sql3 = "TRUNCATE virus";

$sql4 = "TRUNCATE console; UPDATE map SET water = 20000, plants = 20000, wildlife = 10000, farm = 0, ranch = 0, well = 0;";
$sql5 = "UPDATE atmosphere, map SET CoTwo = '0.04', oxygen = 20, methane=0";
$sql6 = "UPDATE timestep SET simTime = '1000-00-00 00:00:00';";
try {
	$db->exec($sql);
	$db->exec($sql2);
	$db->exec($sql3);
	$db->exec($sql4);
	$db->exec($sql5);
	$db->exec($sql6);
for($i = 0; $i < $base_citizens; $i++)
{
	$cit->newCitizens(0,0,mt_rand(90,140),1);
	echo "new citizen! \n";
}
	echo "Reset Was Successful...\n";
	} catch(PDOException $e) { 
	echo die('Reset Failure: '.$e->getMessage()); }
?>
