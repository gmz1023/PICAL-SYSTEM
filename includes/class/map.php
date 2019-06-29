<?php
class map extends citizens
{
	function getCitizenTile($cid)
	{
		$sql = "SELECT tile_id as tid FROM citizens WHERE cid = {$cid}";
		$que = $this->db->prepare($sql);
		try {  
			$que->execute();
			$row = $que->fetch(PDO::FETCH_ASSOC);
			return $row['tid'];
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
		$sql = "SELECT count(cid) as tid FROM citizens WHERE tile_id = {$tid} AND status <> -1";
		$que = $this->db->prepare($sql);
		try {  
			$que->execute();
			$row = $que->fetch(PDO::FETCH_ASSOC);
			return $row['tid'];
		}catch(PDOException $e) { echo $e->getMessage();}
		
	}
	function getMapMax()
	{
		$sql = "SELECT 
				(SELECT sid FROM map ORDER BY sid DESC LIMIT 1) as max_lim,
				(SELECT sid FROM map ORDER BY sid ASC LIMIT 1) as min_lim";
		$que = $this->db->prepare($sql);
		try { $que->execute();
			$row = $que->fetch(PDO::FETCH_ASSOC);
			 return $row;
			}
		catch(PDOException $e) {}
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