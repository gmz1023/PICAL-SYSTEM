<?php
class map extends citizens
{
	function improvements($sid)
	{
		$array = array('well','farm','ranch');
		$choice = $array[mt_rand(0,2)];
		$sql = "UPDATE map SET ".$choice." = ".($choice)."+1 WHERE sid = {$sid} AND ".$choice."+1 < 10 AND (SELECT avg(inti) FROM citizens WHERE tile_id = {$sid}) > 100;";
		try { $this->db->exec($sql);}catch(PDOException $e) { die($e->getMessage());}
	}
	function getTileStats($tid,$d)
	{
		$sql = "SELECT {$d} FROM map WHERE sid = {$tid}";
		$que = $this->db->prepare($sql);
		try { 
			$que->execute();
			$row = $que->fetch(PDO::FETCH_ASSOC);
			return $row;
		}catch(PDOException $e) { die($e->getMessage());}
	}
	function getCitizenTile($cid)
	{
		$sql = "SELECT tile_id as tid FROM citizens WHERE cid = {$cid}";
		$que = $this->db->prepare($sql);
		try {  
			$que->execute();
			if($row = $que->fetch(PDO::FETCH_ASSOC)){
			return $row['tid'];
			}
			else
			{
				return 1;
			}
		}catch(PDOException $e) { echo $e->getMessage();}
	}
	function maxPop($tid)
	{
		$sql = "SELECT max_pop FROM map WHERE sid = {$tid}";
		$que = $this->db->prepare($sql);
		try {  
			$que->execute();
			$row = $que->fetch(PDO::FETCH_ASSOC);
			return $row['max_pop'];
		}catch(PDOException $e) { echo $e->getMessage();}
	}
	function getTilePop($tid)
	{
		$sql = "SELECT count(cid) as tid FROM citizens WHERE tile_id = {$tid} AND status > 0";
		$que = $this->db->prepare($sql);
		try {  
			$que->execute();
			$row = $que->fetch(PDO::FETCH_ASSOC);
			return $row['tid'];
		}catch(PDOException $e) { echo $e->getMessage();}
		
	}
	function getMapMax()
	{
		return array('max_lim'=>6,'min_lim'=>1);
		
	}
	function distanceCheck($tid, $cid)
	{
		$ptid = $tid + 5;
		$mtid = $tid - 5;
		$sql = "SELECT cid FROM citizens WHERE tile_id BETWEEN {$mtid} AND {$ptid}  AND cid <> {$cid} AND relstat <> -1 ";
		
		try { 
			$que = $this->db->prepare($sql);
			$que->execute();
			$cid = [];
			while($row = $que->fetch(PDO::FETCH_ASSOC))
			{
				$cid[] = $row['cid'];
			}
			return $cid;
		}catch(PDOException $e)
		{}
	}

/** Tile Types -- **/
	function totalTypeTiles($t)
	{
		$sql = "SELECT count(type) as ttil FROM map WHERE type = {$t}";
		$que = $this->db->prepare($sql);
		try { $que->execute();
			$row = $que->fetch(PDO::FETCH_ASSOC);
			 return $row['ttil'];
			}catch(PDOException $e){ }
	}
/* Tile Supplies */
	function tileTemp($tid)
	{
		$sql = "SELECT temp FROM map WHERE sid = {$tid}";
		$que = $this->db->prepare($sql);
		try { $que->execute();
				$row = $que->fetch(PDO::FETCH_ASSOC);
			 return $row["temp"];
			}catch(PDOException $e) { }
	}

//* Tile Resources 
	function PlantsOnTile($tid)
	{
		$sql = "SELECT plants FROM map WHERE sid = {$tid}";
		$que = $this->db->prepare($sql);
		try { $que->execute();
				$row = $que->fetch(PDO::FETCH_ASSOC);
			 return $row["plants"];
			}catch(PDOException $e) { }
	}
	function WildlifeOnTile($tid)
	{
		$sql = "SELECT wildlife FROM map WHERE sid = {$tid}";
		$que = $this->db->prepare($sql);
		try { $que->execute();
				$row = $que->fetch(PDO::FETCH_ASSOC);
			 return $row["wildlife"];
			}catch(PDOException $e) { }
	}
	function TotalFood($tid)
	{
		$sql = "SELECT wildlife+plants as food FROM map WHERE sid = {$tid}";
		$que = $this->db->prepare($sql);
		try { $que->execute();
				$row = $que->fetch(PDO::FETCH_ASSOC);
			 return $row["food"];
			}catch(PDOException $e) { }
	}
	function WaterOnTile($tid)
	{
		$sql = "SELECT water FROM map WHERE sid = {$tid}";
		$que = $this->db->prepare($sql);
		try { $que->execute();
				$row = $que->fetch(PDO::FETCH_ASSOC);
			 return $row["water"];
			}catch(PDOException $e) { }
	}
		

}