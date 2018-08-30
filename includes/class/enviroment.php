<?php
class enviroment extends movement
{
	function plants_do_breath()
	{
		/* Ignoring this for now 
				Once the MAP is functional -- this will do math for the number of plants and types of plants
		$sql = "SELECT sum(ox_output) as ox,sum(co_intake) as co FROM plants";
		$que = $this->db->prepare($sql);
		try { $que->execute();
				$row = $que->fetch(PDO::FETCH_ASSOC);
			 $sup = $this->allSuplies();
			 $co = $sup['COTwo'];
			 if($co )
			 	$this->update_specific_supply('air',$row['ox']);
			 	$this->update_specific_supply('COTwo',($row['co']*-1));
			}catch(PDOException $e){}
		*/
		$sql = "SELECT sum(plants) as plants FROM map";
		$que = $this->db->prepare($sql);
		try{
			$que->execute();
			$row = $que->fetch(PDO::FETCH_ASSOC);
			 $sup = $this->allSuplies();
			$co = $row['plants']*2;
			if($sup['COTwo'] >= $co)
			{
				$this->update_specific_supply('COTwo',($co*-1));
				$this->update_specific_supply('air',($co));
				$this->Rain();
			}
		}catch(PDOException $e) { die('ERROR: Plants_Do_Breath');}
	}
/*********************************
		
		Weather Event Functions 
		
********************************/
	function selectEventTile()
	{
		$sql = "SELECT sid FROM map ORDER BY rand() LIMIT 1";
		$que = $this->db->prepare($sql);
		try { 
			
			$que->execute();
			$row = $que->fetch(PDO::FETCH_ASSOC);
			return $row['sid'];
			}catch(PDOException $e) { }
	}
	function Rain()
	{
		$chance = mt_rand(0,100);
		if($chance <= 10)
		{
			$square = $this->selectEventTile();	
			if($this->tileWater($square, mt_rand(1,6)))
			 {
			$text = "[Enviro] It's Raining on {$square}!";
			$this->message($text,'blue',30);
			
			}
		}
	}
}