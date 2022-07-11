<?php
include('includes/db.php');
if(!isset($_GET['status']) || !isset($_GET['page']))
{
	header('location:candles.php?status=1&page=1');
}
else
{
	$status = $_GET['status'];
}
	//* Pagination Stuff I am terrible at, Thanks code-boxx
	define("PER_PAGE", "25"); // ENTRIES PER PAGE
	switch($status)
	{
		case '2':
			$sql = "SELECT CEILING(COUNT(*) / ".PER_PAGE.") `pages` FROM `gravestones`";
			$tbl = 'gravestones';
			$text = 'Dead';
		break;
		case '1':
			$sql = "SELECT CEILING(COUNT(*) / ".PER_PAGE.") `pages` FROM `citizens`";
			$tbl = 'citizens';
			$text = "Alive";
	}
	$stmt = $db->prepare($sql);
	$stmt->execute(); 
	$pageTotal = $stmt->fetch(PDO::FETCH_COLUMN);
	// (C) GET ENTRIES FOR CURRENT PAGE
// (C1) LIMIT (X, Y) FOR SQL QUERY
	$pageNow = isset($_GET["page"]) ? $_GET["page"] : 1 ;
	$limX = ($pageNow - 1) * PER_PAGE;
	$limY = PER_PAGE;
	// * Actual Rest Of the Owl
$sql = "SELECT cid, first_name, last_name, gender,cod,health FROM ".$tbl." ORDER BY cid DESC LIMIT {$limX}, {$limY}";
$que = $db->prepare($sql);
$cit = new base($db,0);
?>
<!DOCTYPE html>
<head>
<title>Not My Candle - Citizen Directory</title>
<link rel="stylesheet" href="style/stylesheets/screen.css">
</head>
<body>
<?php
include('parts/header.php');
	$html = "<div class='reg'>";
	$html .="<div class='top'> {$text} Citizens</div>";
	$html .= "<div class='fb-row'><div class='label'>Name</div><div class='label'>Gender</div><div class='label'>Age</div>";
	if($status == 2) { $html .= "<div class='label'>Cause of Death</div>"; }
	else{ $html .= "<div class='label'>Health</div>"; }
	$html .= "<div class='label'>Actions</div></div>";
try { $que->execute();
	 while($row = $que->fetch(PDO::FETCH_ASSOC))
	 {
		 $cod = $status == 2 ? $row['cod'] : $row['health'];
		 $age = $cit->citizenAge($row['cid']);
		 $html .= "<div class='fb-row'><div class='fb-cell'><a href='history.php?cid={$row['cid']}'>{$row['first_name']} {$row['last_name']}</a></div><div class='fb-cell'>{$row['gender']}</div><div class='fb-cell'>Age {$age} </div><div class='fb-cell'>{$cod}</div><div class='fb-cell'>";
		 if($_GET['status']== '2')
		 {
		 $html .= "<a href='graveyard.php?meth=res&cid={$row['cid']}'>Ressurect</a></div>";
		 }
		 else
		 {
			 $html .="Coming Soon</div>";
		 }
		 $html .= "</div>";
	 }
	 $html .= "</div>";
	}catch(PDOException $e) { die($e->getMessage());}
echo $html;
?>
<div class="nav" id="nav"><?php
	echo get_pagination_links($_GET['page'], $pageTotal, "candles.php?status={$_GET['status']}");
?></div>
