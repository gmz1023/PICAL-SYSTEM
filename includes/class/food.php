<?php
class food extends genes
{
	/*
	
		Need Related Functions
	
	*/
	function updateNeed($cid,$n,$mode)
	{
		$sql = "UPDATE citizens SET {$n} = {$mode} WHERE cid = {$cid}";
		try{
				$this->db->exec($sql);
		}
		catch(PDOException $e)
		{
			die($e->getMessage());
		}
		
	}
	function selectNeed($cid,$need)
	{
		$sql = "SELECT {$need} FROM citizens WHERE cid = {$cid}";
		$que = $this->db->prepare($sql);
		try { 
			$row = $que->fetch(PDO::FETCH_ASSOC);
			return $row[$need];
		}catch(PDOException $e) { die($e->getMessage());}
	}
	function caloricIntake($cid)
	{
		$seq = $this->getParentsGenetics($cid,0)[0];
		$seq = substr($seq,50,2);
		switch($seq)
		{
			case 'AC':
				$con = food_consumption*10;
				break;
			case 'CT':
				$con = food_consumption*.5;
				break;
			case 'GC':
				$con = food_consumption*.100;
				break;
			default:
				$con = food_consumption;
		}
		return $con;
	}
	function fixWater()
	{
		//* Hot Fix
		$sql = "UPDATE
					map
				SET
					water = 0
				WHERE
					water < 0;";
		try { $this->db->exec($sql);}catch(PDOException $e) { die($e->getMessage());}
			
	}
	function satisify_needs($cid,$tid){
		$con = $this->caloricIntake($cid);
		$sql = "UPDATE 
					citizens as c,
					map as m
				SET
					m.water  = m.water-(".water_consumption."),
					c.thirst = 1
				WHERE
					(c.tile_id = m.sid
					AND
					c.thirst < 1
					AND
					c.cid = {$cid})
					AND
					m.water-".water_consumption." > 0;
				";
		
		$sql1 = array(
			"UPDATE 
					citizens as c,
					map as m
				SET
					m.wildlife  = m.wildlife-(".$con."),
					c.hunger = 1
				WHERE
					(c.tile_id = m.sid
					AND
					c.hunger < 1
					AND
					c.cid = {$cid})
					AND
					m.wildlife-(".$con.") >= 0;
				",
			"UPDATE 
					citizens as c,
					map as m
				SET
					m.plants  = m.plants-(".$con."),
					c.hunger = 1
				WHERE
					(c.tile_id = m.sid
					AND
					c.hunger < 1
					AND
					c.cid = {$cid})
					AND
					m.plants-(".$con.") >= 0;
				");
		$sql2 = "UPDATE
					citizens
				SET
					hunger = 0,
					thirst = 0
				WHERE
					cid = {$cid}";
		try {
			if($this->db->exec($sql))
			{
				// They Drank
				$this->healthRestore($cid,6);
			}
			else
			{
				$this->healthHit($cid, '7', 'Thirst');
			}
			$rand = mt_rand(0,1);
			if($this->db->exec($sql1[0]))
			{
				// Eat Animals
				$this->healthRestore($cid,8);
			}
			else
			{
				if($this->db->exec($sql1[1]))
				{
					// Eat Plants
					$this->healthRestore($cid,4);
				}
				else
				{
				//*
				$this->healthHit($cid, '5', 'hunger');
				}
			}
			$this->db->exec($sql2);
		}catch(PDOException $e) { die($e->getMessage());}
	}
}