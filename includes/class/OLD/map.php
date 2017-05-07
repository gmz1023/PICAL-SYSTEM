<?php
class map extends citizens
{
	function getMap($x,$y)
	{
		$sql = "SELECT tileid, tiletype FROM map WHERE x = :x AND y = :y";
		$que = $this->db->prepare($sql);
		$que->bindParam(':x', $x);
		$que->bindParam(':y', $y);
		try { 
				$que->execute(); 
				$array = [];
				while($row = $que->fetch(PDO::FETCH_ASSOC))
				{
					$array[] = $row;	
				}
				return $array;
				}catch(PDOException $e){ echo $e->getMessage(); }	
	}
	function displayMap()
	{
		echo "<br />";
		
		$map = $this->getMap($_GET['x'], $_GET['y']);
		echo "<div id='map'>";
		echo "<div class='row'>";
		$i = 0;
		foreach($map as $key)
		{
			if($i == 100)
			{
				echo "</div><div class='row'>";
				$i = 0;	
			}
			else
			{
				
			}
				echo "<div id='{$key['tileid']}' class='{$key['tiletype']}'><a href='?tid={$key['tileid']}'>&nbsp;</a></div>";
			$i = $i+1;
		}
		echo "</div>";
	}
function displaySubMap()
{
	
}


}