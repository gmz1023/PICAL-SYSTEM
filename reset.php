mwater
<?php
require_once('includes/db.php');
$sql1 = "UPDATE 
			citizens as c, 
			timestep as t,
			supplies as s,
			map as m
		SET 
			s.food = 1000,
			s.water = 1000,
			s.air = 1000,
			s.COTwo = 0,
			s.medicine = 1000,
			c.relstat = 1,
			c.status = 1,
			c.spouse_id = NULL, 
			c.born_on = '0982-00-00 00:00:00', 
			c.health = 100, 
			c.thirst = 1,
			c.hunger = 1,
			c.infected = 0,
			c.pregnant_on = NULL,
			c.cod = NULL,
			c.drankOn = 0,
			m.water = 300,
			m.temp = 74,
			m.wildlife = 300,
			m.plants = 1000,
			m.seeds = 0,
			m.farm = 0,
			m.ranch = 0,
			t.`simTime` = '1000-00-00 00:00:00';";
$sql2 = "DELETE FROM `lurch`.`citizens` WHERE `citizens`.`cid` > 10";
$sql3 = "DELETE FROM `lurch`.`genetics` WHERE `genetics`.`cid` > 10";
$sql4 = "ALTER TABLE citizens AUTO_INCREMENT = 10";
$sql5 = "ALTER TABLE genetics AUTO_INCREMENT = 10";
$sql6 = "TRUNCATE virus";
$sql7 = "UPDATE Atmosphere SET CoTwo = '0.04', oxygen = 20, methane=0";
try {
	
	$db->beginTransaction();
	$db->exec($sql1); 
	$db->exec($sql2);
	$db->exec($sql3);
	$db->exec($sql4);
	$db->exec($sql5);
	$db->exec($sql6);
	$db->exec($sql7);
if($db->commit())
{
	echo "Resetting...\n";
	include('run.php');
}
exit;	
	} catch(PDOException $e) { 
	$db->rollBack();
	echo die('Reset Failure: '.$e->getMessage()); }
