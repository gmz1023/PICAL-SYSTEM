<?php
class death extends supplies
{
	function kill($cid, $r)
	{
		$sql = "UPDATE citizens SET status = -1, died_on = :time, cod = :r WHERE cid = :cid AND status = 1";
		$que = $this->db->prepare($sql);
		$time = $this->getTime();
		$que->bindParam(':cid', $cid);
		$que->bindParam(':time', $time);
		$que->bindParam(':r', $r);
			try { 
				if($que->execute())
				{	
					$this->divorce($cid);
					$this->lifeMessages($cid, $r);	
				}
			}
		catch(PDOException $e) { 
			die($e->getMessage());
		}
	}
	function lastDrankOn($cid)
	{
		$sql = "SELECT drankOn FROM citizens WHERE cid = {$cid}";
		$que = $this->db->prepare($sql);
		try  {
			$que->execute();
			$row = $que->fetch(PDO::FETCH_ASSOC);
			return $row['drankOn'];
		}catch(PDOException $e){}
	}
	function killCitizens($cid, $health, $thirst, $hunger)
	{
		$this->lastDrankOn($cid);
		$tid = $this->getCitizenTile($cid);
		$temp = round($this->tileTemp($tid));

		//* Rewriting this function to solve health issue.
		if($health > 0)
		{
			//* This Function needs to be fully overhauled to properly support everything
			#echo $health."|".$thirst."|".$hunger."\n";
			if($thirst == 1)
			{
				//* This needs to be tweaked to account for water on map
				$drank = $this->lastDrankOn($cid);
				if($drank >= 3)
				{
					$this->kill($cid,"dehydration");
				}

			}
			if($hunger <= 0)
			{
				
				#$this->kill($cid,"Starvation");
			}
			if($temp >= 158 || $temp < -10)
			{
				$this->kill($cid,"Temperature Range");
			}
			if($temp <= 31)
			{
				$this->healthHit($cid, mt_rand(1,5), 'Hypotherma');
			}
			if($temp >= 90)
			{
				$hit = abs(($temp-90));
				$this->healthHit($cid, mt_rand($hit,100), 'Hypertherma');
			}
			else{}
		}
		else
		{
			$this->kill($cid,"Cant Beat the Reaper");
		}
	}
}