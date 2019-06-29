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
			$text = "[HEALTH] {$name} is Thirsty";
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

	}
	function drink($cid,$tid)
	{
					$name = $this->prettyName($cid);
		$water = $this->WaterOnTile($tid);
		if($water >= water_consumption)
		{
			$this->UpdateTileWater($tid, (water_consumption*-1));
			$this->updateThirst($cid,0);

			$text = "[RESOURCE] {$name} DRANK ".water_consumption." Of WATER FROM {$tid}";
			$this->message($text, 'happy', '2');
		}
		else
		{
			$h = $this->getHealth($cid);
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