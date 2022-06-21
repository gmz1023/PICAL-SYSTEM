<?php
class enviroment extends plants
{
/*********************************

Updating Water Tables; Moved "UpdateTileWater" from Map to Here because it makes more sense here
		6/28/2019

*********************************/

/*****************************************

	FUCKING ATMOSPHERIC CONDITIONS


******************************************/
	function updateAtmosphericWater()
	{
		//* This Function should also be used for Evaporation
		$sql = "UPDATE
					map 
				SET
					water = water-(water/temp),
					atmosphericWater = atmosphericWater+(water/temp)
		";
		try {
			$this->db->exec($sql);
		}catch(PDOException $e) { die($e->getMessage());}
	}

	
/*********************************
		
		Weather Event Functions 
		
********************************/

	function selectAverageTemperature()
	{
		$sql = "SELECT avg(temp) as ATemp FROM map";
		$que = $this->db->prepare($sql);
		try { 
			$que->execute();
			$row = $que->fetch(PDO::FETCH_ASSOC);
			return $row['ATemp'];
		}
		catch(PDOException $e){}
	}
	function temperatureGaguge()
	{
		$sql = "SELECT sid,temp FROM map";
		$que = $this->db->prepare($sql);
		try 
		{
			$que->execute();
			while($row = $que->fetch(PDO::FETCH_ASSOC))
			{
				print_r($row);
				$this->updateTemperature($row['sid'],mt_rand(-2,2));
			}
		}catch(PDOException $e)
		{}
	}
	function updateTemperature()
	{
		$sql = "UPDATE 
					map as m,
					Atmosphere as a
				SET 
					m.temp = m.temp+((a.CoTwo+a.methane))
				WHERE 
					m.temp BETWEEN -128 AND 134
				";
		try{
			//$this->db->exec($sql);
			
		}catch(PDOException $e){ 
			print_r($sql);
			die('Temperature Error(E80):'. $e->getMessage());}

	}
	function Temperature($sid)
	{
		$sql = "SELECT temp FROM map WHERE sid = {$sid}";
		$que = $this->db->prepare($sql);
		try {
			$que->execute();
			$row = $que->fetch(PDO::FETCH_ASSOC);
			return $row['temp'];
		}catch(PDOException $e ){ }
	}
	function Rain()
	{
		$sql = "UPDATE map SET water = 0 WHERE water < 0;";
		$sql .= "UPDATE 
					map 
				SET 
					water = water+(water*(2.50*well)) 
				WHERE
					water < (max_pop*".water_consumption."); ";
		$sql .= "
				UPDATE
					map
				SET
					wildlife = ((wildlife+((ranch+1)*1.25))+((ranch+wildlife)*(SELECT count(cid)+1 FROM citizens WHERE tile_id = sid)*0.33))
				WHERE
					water-((wildlife+((ranch+1)*1.25))+((ranch+wildlife)*(SELECT count(cid)+1 FROM citizens WHERE tile_id = sid)*0.33)) > 0
					and
					((wildlife+((ranch+1)*1.25))+((ranch+wildlife)*(SELECT count(cid)+1 FROM citizens WHERE tile_id = sid)*0.33)) < (max_pop*".food_consumption.")
					;";
		/*
		
			Plant Stuff
		
		*/
		$sql .= "
				UPDATE map
					SET
					plants = plants*1.25+farm
				WHERE	
					water-((plants+(farm+1)*30)+farm) > 0;";
		$sql .= "
				UPDATE
					map
				SET
					water = water-((wildlife*70)+(plants*20))
				WHERE
					water-((wildlife*70)*(plants*20)) > 0;";
		
		try { $this->db->exec($sql);}catch(PDOException $e) { die($e->getMessage());}
	}

}