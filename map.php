<?php
include('includes/db.php');
function citizens($tile_id,$db)
{
	$sql = "SELECT first_name, last_name FROM citizens WHERE tile_id = :tid";
	$que = $db->prepare($sql);
	$que->bindParam(':tid',$tile_id);
	$html = "<li>Citizens<ul>";
	try { 
		$que->execute();
		while($row = $que->fetch(PDO::FETCH_ASSOC))
		{
			$html .= "<li>{$row['first_name']} {$row['last_name']}</li>";
		}
		$html .= "</ul></li>";
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
	$html = "<ul>";
	while($row = $que->fetch(PDO::FETCH_ASSOC))
	{
		$html .= "<li>{$row['sid']}
					<ul>
						<li>Tile Type: {$row['type']}</li>
						<li>Water  : {$row['water']}</li>
						<li>Temp   : {$row['temp']}</li>
						<li>Plants : {$row['plants']}</li>
						<li>Seeds  : {$row['seeds']}</li>
						<li>animal : {$row['wildlife']}</li>
						<li>Fertile: {$row['fertile']}</li>
						<li>Max Pop: {$row['max_pop']}</li>
						<li>Ranches:{$row['ranch']}</li>
						<li>Farms  : {$row['farm']}</li>
						<li>Wells  : {$row['well']}</li>";
		$html .= citizens($row['sid'],$db);
		
		
		$html .="</ul></li>";
	}
	$html .= "</ul>";
}catch(PDOException $e) { die($e->getMessage());}

echo $html;