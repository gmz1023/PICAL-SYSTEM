<?php
include('includes/db.php');
?>
<!DOCTYPE html><head>
<title>Citizen History</title>
</head>
<?php
$cit = new base($db,0);
$time = $cit->getTime();
$sql = "SELECT * FROM citizens WHERE cid = :cid";
$que = $db->prepare($sql);
$que->bindParam(":cid",$_GET['cid']);
try { 
	$que->execute();
	$row = $que->fetch(PDO::FETCH_ASSOC);
}catch(PDOException $e) { 
	die($e->getMessage());}
		$mother = $row['mother_id'] == 0 ? 'god' : $row['mother_id']; 
		$father = $row['father_id'] == 0 ? 'god' : $row['father_id'];
		$spouse = $row['spouse_id'] == 0 ? 'NA'  : $row['spouse_id']; 
?>


<table>
	<tbody>
		<tr><th>Basic Information</th></tr>
		<tr><th>Name</th><td><?= $row['first_name'].' '.$row['last_name'] ?></td></tr>
		<tr><th>Biological Sex</th><td><?= $row['gender'] ?></td></tr>
		<tr><th>Age</th><td><?=
				$cit->citizenAge($_GET['cid']);
			?></td></tr>
		<tr><th colspan=3>Parents</th></tr>
		<tr>
			<th>Mother</th><td><?= $mother ?></td>
			<th>Father</th><td><?= $father ?></td>
		</tr>
		<tr><th>Spouse ID</th><td><?= $spouse ?></td></tr>
		<tr><th>Intelligence</th><td><?= $row['inti'] ?></td></th></tr>
		<tr><th colspan=3>Health Info</th></tr>
		<tr><th>Health</th><td><?= $row['health'] ?></td><th>Infected</th><td><?= $row['infected'] ?></td></tr>
	</tbody>
	<tfoot>
		<tr><td><a href='index.php'>Back To Log</a></td></tr>
	</tfoot>
</table>