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
			$this->db->beginTransaction();
			$que = $this->db->prepare($sql);
			$que->execute();
			$cid = [];
			while($row = $que->fetch(PDO::FETCH_ASSOC))
			{
				$cid[] = $row['cid'];
			}
			$this->db->commit();
			return $cid;
		}catch(PDOException $e)
		{}
	}

/** Tile Types -- **/
	function totalTypeTiles($t)
	{
		$sql = "SELECT sum(type) FROM map WHERE type = {$t}";
		$que = $this->db->prepare($sql);
		try { $que->execute();}catch(PDOException $e){ }
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
	function UpdateTileWater($tid, $a)
	{
		$sql = "UPDATE map SET water = water+{$a} WHERE sid = {$tid}";
		try { $this->db->exec($sql);}catch(PDOException $e) { }
		return true;
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