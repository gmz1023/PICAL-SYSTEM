<?php
include('includes/db.php');
function citizens($tile_id,$db)
{
	$sql = "SELECT count(*) as citcount FROM citizens WHERE tile_id = :tid";
	$que = $db->prepare($sql);
	$que->bindParam(':tid',$tile_id);

	try { 
		$que->execute();
		$row = $que->fetch(PDO::FETCH_ASSOC);
		return $row['citcount'];
	}catch(PDOException $e) { die($e->getMessage());}
	return $html;
}
$sql = "SELECT 
			* 
		FROM 
			map";
$que = $db->prepare($sql);
try { 
	$que->execute();
	$html = "";
	while($row = $que->fetch(PDO::FETCH_ASSOC))
	{
		switch($row['type'])
		{
			case '2':
				$type = 'water';
				$cit = '';
				break;
			case '1':
				$type = 'land';
				$cit = citizens($row['sid'],$db);
				break;
			default: 
				$type = 'land';
				$cit = citizens($row['sid'],$db);
		}
		if($cit > 0)
		{
			$type .= " occupied";
		}
		
		$html .= "<div class='tile {$type}'><a href=''>{$cit}</a></div>";
		
	}
}catch(PDOException $e) { die($e->getMessage());}
?>
<!DOCTYPE html>
<html>
<head>
	<title>PICAL Map</title>
</head>
<link rel='stylesheet' href="style/stylesheets/screen.css">
	<body>
<div id='container'>
<div class='header'>
	<div class='logo'><img src="style/img/shutterstock_304609643.jpg" width='50px' height='50px' /> Not My Candle</div>
	<menu><div class='lfmn'><a href='candles.php?status=1&page=1'>Lit Candles</a> <a href='candles.php?status=2&page=1'>Extinguished Cnadles</a></div><div class='rgmn'><a href='contribute.php'>Information</a><a href='index.php'>Back To Main</a></div></menu>
</div>
	<div id='map'>
	<?php
	echo $html;
	?>
	</div>
	</body>
</html>