<?php
include('includes/db.php');
if(!isset($_GET['meth']))
{
$sql = "SELECT cid, first_name, last_name, gender FROM gravestones";
$que = $db->prepare($sql);
$html = "<table><tr colspan='3'><th>Dead Citizens</th></tr>";
$html .= "<tr><th>first name</th><th>Last Name</th><th>Gender</th></tr>";
try { $que->execute();
	 while($row = $que->fetch(PDO::FETCH_ASSOC))
	 {
		 $html .= "<tr><td>{$row['first_name']}</td><td>{$row['last_name']}</td><td>{$row['gender']}</td><td><a href='graveyard.php?meth=res&cid={$row['cid']}'>Ressurect</a></td></tr>";
	 }
	 $html .= "</table>";
	}catch(PDOException $e) { die($e->getMessage());}
echo $html;
}
else
{
	if(!isset($_GET['cid']) || !is_numeric($_GET['cid']))
	{
		header('location:graveyard.php');
	}
	$cid = $_GET['cid'];
	switch($_GET['meth'])
	{
		case 'res':
			$sql = "INSERT INTO citizens SELECT * FROM gravestones WHERE cid = {$cid};";
			$sql2 = "DELETE FROM gravestones WHERE cid = {$cid}";
			$sql3 = "INSERT INTO genetics SELECT genome FROM dead_dna WHERE cid = {$cid}";
			$sql4 = "DELETE FROM dead_dna WHERE cid = {$cid}";
			try {
				$db->beginTransaction();
				$db->exec($sql);
				$db->exec($sql2);
				$db->exec($sql3);
				$db->exec($sql4);
				$db->commit();
				header("location:graveyard.php");
			}catch(PDOException $e) { die($e->getMessage());
									$db->rollBack();
									}
		default:
			header("location:graveyard.php");
			break;
	}
}