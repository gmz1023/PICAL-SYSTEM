<?php
include('includes/db.php');
?>
<!DOCTYPE html><head>
<title>Candle History</title>
</head>

<link rel='stylesheet' href='style/stylesheets/screen.css'>
<?php
$cit = new base($db,0);
$cid = $_GET['cid'];
$time = $cit->getTime();
$sql = "SELECT 
			*
		FROM 
			citizens 
		WHERE cid = :cid";
$sql2 = "SELECT 
			*
		FROM 
			gravestones
		WHERE cid = :cid";
$que = $db->prepare($sql);
$que->bindParam(":cid",$cid);
try { 
	$que->execute();
	$row = $que->fetch(PDO::FETCH_ASSOC);
	if($row)
	{
		// Do Nothing
	}
	else
	{
		$que = $db->prepare($sql2);
		$que->bindParam(':cid',$cid);
		try { 	
			$que->execute();
			$row = $que->fetch(PDO::FETCH_ASSOC);
		}catch(PDOException $e) { die($e->getMessage());}
	}
	
}catch(PDOException $e) { 
	die($e->getMessage());}
		$name   = $cit->prettyName($cid);
		$mother = $row['mother_id'] == 0 ? 'god' : $cit->prettyName($row['mother_id']); 
		$father = $row['father_id'] == 0 ? 'god' : $cit->prettyName($row['father_id']);
		$spouse = $row['spouse_id'] == 0 ? 'NA'  : $cit->prettyName($row['spouse_id']);
		$age = 				$cit->citizenAge($_GET['cid'],1);
		$infected = $row['infected'] == 0 ? 'No' : 'Yes';
?>
<body>
<?php include('parts/header.php'); ?>
	<div id='history'>
			<div class='profile-pic'>
			<img src="style/img/shutterstock_304609643.jpg">
				<h2><?= $name; ?></h2>
			</div>
		<section class='bi'>
			<h1>Basic Information</h1>
			<dl>
				<dt>Biological Sex</dt>
				<dd><?= $row['gender'] ?></dd>
				<dt>DOB</dt>
					<dd><?= $row['born_on'] ?></dd>
				<dt>Age</dt>
				<dd><?= $age ?></dd>
			</dl>
		</section>
		<section>
				<h1>Relations</h1>
			<dl>	
			<dt>Father</dt>
				<dd><?= $father ?></dd>
			<dt>Mother</dt>
				<dd><?= $mother ?></dd>
			<dt>Spouse</dt>
				<dd><?= $spouse; ?></dd>
				<!-- This Space Reserved for Children, Eventually !-->
			</dl>
		</section>
		<section class='stat'>
			<h1>Stats</h1>
			<dl>
				<dt>Health</dt>
				<dd><?= $row['health'] // Need to add Hearts Icons or something here ?></dd>
				<dt>Intelligence</dt>
				<dd><?= $row['inti'] ?></dd>
				<dt>Infected?</dt>
				<dd><?= $infected ?></dd>
			</dl>
		</section>
		<section class='action-bar'>
		<h2>Actions</h2>
		Reignite | Extinguish | Alter | Gift
		</section>
	</div>
<?php include('parts/footer.php'); ?>