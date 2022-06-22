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
				break;
			case '1':
				$type = 'land';
				break;
			default: 
				$type = 'land';
		}
		$cit = citizens($row['sid'],$db);
		$html .= "<div class='tile {$type}'>{$row['temp']}|{$row['sid']} | {$cit}</div>";
		
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
	<div id='map'>
<?php
echo $html;
?>
	</div>
	</body>
</html>