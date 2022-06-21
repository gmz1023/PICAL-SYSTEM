<?php 
class food extends genes
{
/* 
	Food / Need related Functions 
*/
	function updateThirst($cid,$mode,$tid = NULL)
	{
		$sql = "UPDATE citizens SET thirst = {$mode} WHERE cid = {$cid}";
		try {
			if($this->db->exec($sql))
			{
				if(!is_null($tid))
				{
					$this->UpdateTileWater($tid, (water_consumption*-1));
					return true;
				}
				else{ return true;}
			}
			
		}catch(PDOException $e){
			
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

			$this->updateHunger($cid, 1);
			$text = "[HEALTH] {$name} is Hungry";
			$this->message($text, 'red', '2');
			#die();
		}
		if($this->isThirsty($cid) == 1)
		{
			$this->drink($cid,$tid);
		}
		else
		{
			$this->updateThirst($cid, 1);
				$text = "[HEALTH] {$name} is Thirsty";
				$this->message($text, 'red', '2');
					#die();
		}
		
	}
	function eat($cid,$tid)
	{
		$h = $this->getHealth($cid);
		$name = $this->prettyName($cid);
		$wl = $this->WildlifeOnTile($tid);
		$pl = $this->PlantsOnTile($tid);
		//* There might eventually be a "Physically Active" flag to determine how much food/water is eatan
		$ea = food_consumption;
		switch(mt_rand(0,1))
		{
			case 0:
				if($pl > $ea)
				{
				$sql = "UPDATE 
							map
						SET 
							plants = plants-{$ea},
							seeds = seeds+({$ea})
						WHERE 
							sid = {$tid}
							AND
							(plants-{$ea}) > 0
							";
				try {	if($this->db->exec($sql))
					{	
					 	$food = 'plant';
						
						$text = "[RESOURCE]{$name} ATE {$ea}".weight_units." {$food}";
						$this->message($text,'green',30);
					$this->updateHunger($cid,0);
					
					}
					}catch(PDOException $e) { die($e->getMessage());}

				}
				else
				{
					$this->farming($ea, $cid, $tid);
					$dx = $this->deathMessage($cid, 5,'food');
					$this->healthHitSilent($cid,5);
					
				}
				break;
			case 1:
				if($wl > $ea)
				{
				$sql= "UPDATE map SET wildlife = wildlife-{$ea} WHERE sid = {$tid}";
				try { $this->db->exec($sql);
					 $food = 'animal';
					$text = "[RESOURCE]{$name} ATE {$ea} {$food}";
					$this->message($text,'green',30);
					$this->updateHunger($cid,0);
					 $this->healthRestore($cid,5);
					}catch(PDOException $e) { die($e->getMessage());}
					
				}
				else
				{
					$this->husbandry($tid,mt_rand(4,8));
					$text = "[RESOURCE] {$name} WENT A DAY WITHOUT FOOD AND LOST 1/{$h} HEALTH";
					$this->message($text, 'red', '2');
					$this->healthHitSilent($cid,5);
				}
				break;
		}

	}
	function drink($cid,$tid)
	{
		$water = $this->WaterOnTile($tid);
		$consu = water_consumption;
		if($water > $consu)
		{
			if($this->updateThirst($cid,1000,$tid))
			{
				#die('THEY DRANK!');
			}
			#die('MORE WATER!!!!!');
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