<?php 
class food extends genes
{
/* 
	Food / Need related Functions 
*/
	function updateThirst($cid,$mode)
	{
		$sql = "UPDATE citizens SET thirst = {$mode} WHERE cid = {$cid}";
		try { $this->db->exec($sql);}catch(PDOException $e){
			
		}
	}
	function isThirsty($cid)
	{
		$sql = "SELECT thirst FROM citizens WHERE cid = {$cid}";
		$que = $this->db->prepare($sql);
		try { $que->execute();
				$row = $que->fetch(PDO::FETCH_ASSOC);
			 return $row['thirst'];
			}
		catch(PDOException $e) {}
	}
	function updateHunger($cid,$mode)
	{
		$sql = "UPDATE citizens SET hunger = {$mode} WHERE cid = {$cid}";
		try { $this->db->exec($sql);}catch(PDOException $e){
			
		}
	}
	function isHungry($cid)
	{
		$sql = "SELECT hunger FROM citizens WHERE cid = {$cid}";
		$que = $this->db->prepare($sql);
		try { $que->execute();
				$row = $que->fetch(PDO::FETCH_ASSOC);
			 return $row['hunger'];
			}
		catch(PDOException $e) {}
	}
	function satisify_needs($cid)
	{
		$tid = $this->getCitizenTile($cid);
		$name = $this->prettyName($cid);
		if($this->isHungry($cid) == 1)
		{
			$this->eat($cid,$tid);
		}
		else
		{
			if(mt_rand(0,10) >= 4)
			{
			$this->updateHunger($cid, 1);
			$text = "[HEALTH] {$name} is Hungry";
			$this->message($text, 'red', '2');
			#die();
			}
		}
		if($this->isThirsty($cid) == 1)
		{
			$this->drink($cid,$tid);
		}
		else
		{
			if(mt_rand(0,10) <= 8)
			{
			$this->updateThirst($cid, 1);
				$text = "[HEALTH] {$name} is Thirsty";
				$this->message($text, 'red', '2');
					#die();
			}
		}
		
	}
	function eat($cid,$tid)
	{
		$h = $this->getHealth($cid);
		$name = $this->prettyName($cid);
		$wl = $this->WildlifeOnTile($tid);
		$pl = $this->PlantsOnTile($tid);
		//* There might eventually be a "Physically Active" flag to determine how much food/water is eatan
		$ea = mt_rand(4,6)*TIME_CHOICE;
		switch(mt_rand(0,1))
		{
			case 0:
				if($pl > $ea)
				{
				$sql = "UPDATE map SET plants = plants-{$ea} WHERE sid = {$tid}";
				try {	$this->db->exec($sql);
					 	$food = 'plant';
						$text = "[RESOURCE]{$name} ATE {$ea} {$food}";
						$this->message($text,'danger',30);
						$this->updateHunger($cid,0);
					}catch(PDOException $e) { die($e->getMessage());}

				}
				else
				{
					$this->farming($ea, $cid, $tid);
					$text = "[RESOURCE] {$name} WENT A DAY WITHOUT FOOD AND LOST 1/{$h} HEALTH";
					$this->message($text, 'red', '2');
					$this->healthHitSilent($cid,-1);
				}
				break;
			case 1:
				if($wl > $ea)
				{
				$sql= "UPDATE map SET wildlife = wildlife-{$ea} WHERE sid = {$tid}";
				try { $this->db->exec($sql);
					 $food = 'animal';
					$text = "[RESOURCE]{$name} ATE {$ea} {$food}";
					$this->message($text,'danger',30);
					$this->updateHunger($cid,0);
					}catch(PDOException $e) { die($e->getMessage());}
					
				}
				else
				{
					$this->husbandry($tid,mt_rand(4,8));
					$text = "[RESOURCE] {$name} WENT A DAY WITHOUT FOOD AND LOST 1/{$h} HEALTH";
					$this->message($text, 'red', '2');
					$this->healthHitSilent($cid,-1);
				}
				break;
		}

	}
	function drink($cid,$tid)
	{
		$h = $this->getHealth($cid);
		$name = $this->prettyName($cid);
		$water = $this->WaterOnTile($tid);
		//* There might eventually be a "Physically Active" flag to determine how much food/water is eatan
		$wc = (water_consumption*mt_rand(1,4))*TIME_CHOICE;
		if($water >= $wc)
		{
			$this->UpdateTileWater($tid, ($wc*-1));
			$this->updateThirst($cid,0);

			$text = "[RESOURCE] {$name} DRANK ".$wc." Of WATER FROM {$tid} | GAINED 1hp ";
			$this->message($text, 'happy', '2');
		}
		else
		{

			$text = "[RESOURCE] {$name} WENT A DAY WITHOUT WATER AND LOST 1/{$h} HEALTH";
			$this->message($text, 'red', '2');
			$this->healthHitSilent($cid,-1);
		}
	}
	function allSuplies()
	{
		$sql = "SELECT * FROM supplies";
		$que = $this->db->prepare($sql);
		try { $que->execute();
			$row = $que->fetch(PDO::FETCH_ASSOC);	
			 return $row;
			}catch(PDOException $e) { echo $e->getMessage();}
	}
	function supplyCheck($name)
	{
		$sql = "SELECT {$name} FROM supplies";
		$que = $this->db->prepare($sql);
		try { $que->execute();
			$row = $que->fetch(PDO::FETCH_ASSOC);	
			 return $row[$name];
			}catch(PDOException $e) { echo $e->getMessage();}
	}
	
}