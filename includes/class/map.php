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
	function distanceCheck($tid, $cid)
	{
		$ptid = $tid + 5;
		$mtid = $tid - 5;
		$sql = "SELECT cid FROM citizens WHERE tile_id BETWEEN {$mtid} AND {$ptid}  AND cid <> {$cid} ";
		
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

}