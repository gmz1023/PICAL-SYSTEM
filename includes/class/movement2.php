<?php
class movement extends bacteria
{
	function playerMove($cid)
	{
		
		/* 
		
		
		
		This needs to be modified to account for Ocean Tiles
		
		
		
		*/
		
		$limits = $this->getMapMax();
		$sid = $this->getCitizenTile($cid);
		//* Search for Water / Food
		
		//* There has Got to be an easier way to do this, but until i figure it out, here we go\
		$data = $this->getTileStats($sid,'water,temp,max_pop');
		$p01 = $data['water'];
		$temp = $data['temp'];
		$pop = $this->getTilePop($sid);
		$max_pop = $data['max_pop'];;

		if($temp <= bio_temp_min || $temp >= bio_temp_max)
		{
			$r = "Temperature";
			$move = mt_rand(-1,1);
			//* There is no water below this temp. need to work on ICE math
			$this->healthHit($cid,-4,$r);
		}
		else
		{
			$food = $this->TotalFood($sid);
			if($p01 <= water_consumption || $food == 0)
			{
				$r = 'Resources';
				// Leave to find more water
				$move = mt_rand(-1,1);
			}
			elseif($max_pop <= $pop)
			{
				$r = 'population';
			$move = mt_rand(-1,1);
			
			}
			else
			{
				$r = '';
				$move = 0;
			}
		}
		$pos = $sid+$move;
		if($pos >= $limits['max_lim'])
		{
			$pos = 1;
		}
		if($pos <= 0)
		{
			$pos = $limits['min_lim']+1;
		}
		$this->updatePos($cid,$pos, $move, $r);
		$this->satisify_needs($cid,$sid);
	}
	function updatePos($cid,$pos, $move, $r)
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
				}catch(PDOException $e) {die($this->getMessage());}
			$text = "[MOVEMENT]{$name}";
			if(!empty($spouse) || !is_null($spouse))
			{
				$text .= " AND {$Spname} ";	
			}
			$text .=" MOVED {$move} SPACES TO {$pos} | {$r}";
			if($move == 0)
			{
			}
			else
			{
			$this->message($text,'blue',3);
			}
		}
	}
	
}