<?php
class tribe extends health
{
	/************************************************
		
		
			Tribe Functions 
				Used to help negate baby booms
		
		
		******************************************/
	function tribesmen($tid)
	{
		$sql = "SELECT cid FROM citizens WHERE tribe_id = :tid";
		$que = $this->db->prepare($sql);
		$que->bindParam(":tid", $tid);
		try { $que->execute(); }catch(PDOException $e) {}
	}
}