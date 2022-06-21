<?php
class family extends pregancy
{
	/*
	
		Manages Families 
			Disallows Incest (Probably going to be opition?)
			Manages Genetic Disorders (AIDS, Cancers, ETC)
			
	*/
	function getSpouse($cid)
	{
		$sql = "SELECT cid FROM citizens WHERE spouse_id = {$cid}";
		$que = $this->db->prepare($sql);
		try { $que->execute();
				$row = $que->fetch(PDO::FETCH_ASSOC);
			 return $row['cid'];
			}
		catch(PDOException $e){}
	}
	function getParents($cid)
	{
		$sql = "SELECT mother_id as mom, father_id as dad FROM citizens WHERE cid = :cid";
		$que = $this->db->prepare($sql);
		$que->bindParam(":cid", $cid);
		try { 
			$que->execute();
			$row = $que->fetch(PDO::FETCH_ASSOC);

		}catch(PDOException $e) {}
	}	
	function genderedSingles($g)
	{
		$sql = "SELECT DISTINCT cid FROM citizens WHERE gender = '{$g}' AND relstat = 1 ORDER BY RAND()";
		$que = $this->db->prepare($sql);
		try { 
				$que->execute();
				$array = [];
				while($row = $que->fetch(PDO::FETCH_ASSOC))
				{
					$array[] = $row['cid'];
				}
		}catch(PDOException $e) { echo $e->getMessage();}
		return $array;
	}
	function updateSpouse($cit1,$cit2)
	{
			$sql = "UPDATE citizens SET spouse_id = '{$cit1}', relstat = '2' WHERE cid = '{$cit2}' AND cid <> {$cit1}; ";
			try { $this->db->exec($sql);}catch(PDOException $e) { echo $e->getMessage();}
	}

	function divorce($cid)
	{
		//* Right now this is used for Dead People
		$sql = "UPDATE citizens SET relstat = 1 WHERE (cid = {$cid} AND status = 1) OR (spouse_id = {$cid}) AND spouse_id <> 0;";
		$que = $this->db->prepare($sql);
		try { $que->execute();} catch(PDOException $e) { die($e->getMessage());}
	}
	function marryCitizens()
	{
	}
}