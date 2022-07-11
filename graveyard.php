<?php
include('includes/db.php');
if(!isset($_GET['cid']) || !is_numeric($_GET['cid']))
	{
		header('location:graveyard.php');
	}
else
{
	$cid = $_GET['cid'];
	switch($_GET['meth'])
	{
		case 'res':
			$sql = "UPDATE gravestones SET born_on = (SELECT simTime FROM timestep LIMIT 1) WHERE cid = {$cid}; ";
			$sql .=  "INSERT INTO citizens SELECT * FROM gravestones WHERE cid = {$cid};";
			$sql2 = "DELETE FROM gravestones WHERE cid = {$cid}";
			$sql3 = "INSERT INTO genetics SELECT * FROM dead_dna WHERE cid = {$cid}";
			$sql4 = "DELETE FROM dead_dna WHERE cid = {$cid}";
			try {
				$db->beginTransaction();
				$db->exec($sql);
				$db->exec($sql2);
				$db->exec($sql3);
				$db->exec($sql4);
				$db->commit();
			header("location:candles.php?status=1");
			}catch(PDOException $e) { die($e->getMessage());
									$db->rollBack();
									}
		default:
			header("location:candles.php?status=1");
			break;
	}
}