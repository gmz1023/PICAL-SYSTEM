<?php
class plants extends animals
{
	function killPlants()
	{
		$sql = "UPDATE map SET plants = plants-".mt_rand(0,1)." WHERE temp > 100";
	}
	function countPlants()
	{
		$sql = "SELECT sum(plants) as plants FROM map";
		$que = $this->db->prepare($sql);
		try {
			$que->execute();
			$row = $que->fetch(PDO::FETCH_ASSOC);
			return $row['plants'];
		}catch(PDOException $e){
			
		}
	}
//* Moving Functions to Proper Channels
	function plants_do_breath($co)
	{
		//* This Function needs to be renamed/moved to better represent that it works for Ocean Oxygenation as well
		$sql = "SELECT sum(plants) as plants FROM map";
		$que = $this->db->prepare($sql);
		try{
			$que->execute();
			$row = $que->fetch(PDO::FETCH_ASSOC);
			 $sup = $this->allSuplies();
			$ocean = $this->totalTypeTiles(1);
			$water = $this->waterReserver();
			$plankton = $water*$ocean;
			$plants = $row['plants'];
			$oxygen = (($plants*$plankton)*2)*TIME_CHOICE;
			/* This Function needs to be tweaked at some point
				Having it check for the CO2 levels and Oxygen Levels
				causes everyone to die from suffication. Which is not good
			*/
				$this->update_specific_supply('COTwo',($co*-1));
				$this->update_specific_supply('air',($oxygen));
			$this->Rain();
			$this->plantGrowth();
		}catch(PDOException $e) { die('ERROR: Plants_Do_Breath');}
	}
	function plantGrowth()
	{
		/** 
			This Will Be Updated eventually to effect each square seperately
			Plant Growth needs to be modified to effect overall population growth as well.
		*/
		$pop = $pop = $this->totalPopulation();
		$growth = (mt_rand(1,20)*TIME_CHOICE);
		if(mt_rand(0,250) <= 200)
		{
		$sql = "UPDATE
					map
				SET
					plants = (plants+{$growth}*(farm+1))
				WHERE
					temp BETWEEN 40 and 90
                    AND water <> 0;";
		try {
			$this->db->exec($sql);
			$this->plantDeath();
			$c = $this->countPlants();
			$this->message("[Enviro]Plant Growth! [Number]{$c}",'blue',30);
		}catch(PDOException $e){}
		}
	}
	function plantDeath()
	{
		$sql = "UPDATE
					map
				SET
					plants = plants-1
				WHERE
					temp >= 95
					OR
					temp >= 30
					OR
                    water <> 0;";
		try {
			$this->db->exec($sql);
			$this->message('[Enviro]Plants Grew!','blue',30);
		}catch(PDOException $e){}
	}
	function farming($e, $cid, $sid)
	{
		$e = $e/2;
		$sql = "UPDATE 
					map
				SET
					plants = plants+{$e},
					farm = farm+1
				WHERE
					sid = {$sid}
					";
		try { 
				$this->db->exec($sql);
				$text = "[FARMING] FARM STARTED {$e} PLANTED";
				$this->message($text,'green',30);
		}
			catch(PDOException $e){ die($e->getMessage());}
	}
	//* 
}