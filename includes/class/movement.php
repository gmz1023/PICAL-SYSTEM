<?php
class movement
{
	function playerMove($cid)
	{
		$limits = $this->getMapMax();
		$sid = $this->getCitizenTile($cid);
		//* Search for Water / Food
		
		//* There has Got to be an easier way to do this, but until i figure it out, here we go
		$p01 = $this->WaterOnTile($sid);
		$temp = $this->tileTemp($sid);
		if($temp <= 31 || $temp >= 95)
		{
			$move = mt_rand(-1,1);
			//* There is no water below this temp. need to work on ICE math
			$this->satisify_needs($cid);
		}
		else
		{
			if($p01 <= water_consumption)
			{
				// Leave to find more water
				$move = mt_rand(-1,1);
				$this->satisify_needs($cid);
			}
			else
			{
				$move = 0;
				$this->satisify_needs($cid);
			}
		}
		$pos = $sid+$move;
		#echo "{$pos} \n";
		if($pos >= $limits['max_lim']+1)
		{
			$pos = 1;
		}
		if($pos <= 0)
		{
			$pos = $limits['max_lim']+1;
		}
		$this->updatePos($cid,$pos, $move);
		 #die();
	}
	function updatePos($cid,$pos, $move)
	{
		$age = $this->citizenAge($cid);
		$momID = $this->getParents($cid);
		if($age < 18 && $momID <> 0)
		{
			// Only Move Near Parents
			
			$momTile = $this->getCitizenTile($momID);
			$sql = "UPDATE
						citizens
					SET
						tile_id = {$momTile}
					WHERE
						cid = {$cid};
			";
			try { 
				$this->db->beginTransaction();
				$this->db->exec($sql);
				$this->db->commit();
			}catch(PDOException $e){ $this->db->rollback();}
		}
		else
		{
			$spouse = $this->getSpouse($cid);
			if(!empty($spouse) || !is_null($spouse))
			{
			$spCheck = $this->checkStatus($spouse);
			$Spname = $this->prettyName($spouse);
			}
			else
			{
				$spCheck = false;
			}
			$check = $this->checkStatus($cid);
			$name = $this->prettyName($cid);

			if($check == 2)
			{
				echo "{$name} is pregnanati";
			}
			if($spCheck == 2)
			{
			#	echo "{$Spname} is Preganant \n";
			}
			$sql = "UPDATE
						citizens
					SET
						tile_id = {$pos}
					WHERE
						cid = {$cid}
						OR
						spouse_id = {$cid};
						";
			try { $this->db->exec($sql);
				}catch(PDOException $e) {}
			$text = "[MOVEMENT]{$name}";
			if(!empty($spouse) || !is_null($spouse))
			{
				$text .= " AND {$Spname} ";	
			}
			$text .="MOVED {$move} SPACES TO {$pos}";
			$this->message($text,'blue',30);
			
		}
	}
	
}