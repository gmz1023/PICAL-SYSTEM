a.<?php
class enviroment extends plants
{
/*********************************

Updating Water Tables; Moved "UpdateTileWater" from Map to Here because it makes more sense here
		6/28/2019

*********************************/
function UpdateTileWater($tid, $a, $lr = null)
{
	$a = round($a);
		$sql = "UPDATE 
					map as m,
					Atmosphere as a
				SET 
					m.water = m.water+{$a},
					";
			if($a < 0)
			{
				$sql .= "m.lastStorm = m.lastStorm+1";
			}
			else
			{
				$sql .= "m.lastStorm = 0,
					m.temp = m.temp-(m.temp*0.15)
				";
			}
		$sql .= 
				" WHERE 
					sid = {$tid} AND m.water+{$a} >= 0
					AND
					m.water > 0
					";
		try { 
			if($this->db->exec($sql))
			{
			return true;
			}
			else
		 	{	
				return false;
			}
			}catch(PDOException $e) {
			echo "\n";
			echo print_r($sql);
			echo "\n";
			die("Water Error(Ev48):".$e->getMessage()); }
		
}
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
			$this->db->exec($sql);
			
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
		$this->WildLifeReproduction();
		$sql = "SELECT sid, temp, water,lastStorm FROM map";
		$que = $this->db->prepare($sql);
		try { $que->execute();
				while($row = $que->fetch(PDO::FETCH_ASSOC))
				{
					$days = mt_rand(0,TIME_CHOICE);
					$dur = mt_rand(0,24);
					$ints = mt_rand(0,10);
					$rain = (($dur*$ints)*$days)*1000;
					$rainCoun = 65;
					$dr= $row['lastStorm']+1;
					if(mt_rand(0,$dr) == $dr)
					{
						if($row['temp'] > 39 && $row['temp'] < 75)
						{
							$this->UpdateTileWater($row['sid'], $rain);
						}
						if($row['temp'] > 90)
						{
							$tmath = abs(($row['temp'])-212);
							$loss = $tmath > 0 ? $rain/$tmath : $rain*0.001;
							$loss = $loss*-1;
							#echo $loss."\n";
							$this->UpdateTileWater($row['sid'], $loss);
						}
						if($row['temp'] < 31)
						{
							$tmath = ($row['temp'])-31;
							$loss = $tmath > 0 ? $rain/$tmath : $rain*0.001;
							$loss = $loss <> 0 ? $loss*-1 : $loss;
							#echo $loss."\n";
							$this->UpdateTileWater($row['sid'], $loss);
						}
						else
						{
							//
							
						}
					}
					else
					{
						$loss = mt_rand(0,$rain)*-1;
						$this->UpdateTileWater($row['sid'], $loss);}
					$this->updateTemperature();
				}
			 
			}catch(PDOException $e){ die("Rain Error(E119):".$e->getMessage());}
		$this->plants_do_breath();
		#$this->updateAtmosphericWater();
	}
	function RainOLD()
	{
		/****************************************
		
		THIS FUNCTION NEEDS TO BE CLEANED UP FOR CLARITY
		
		
		****************************************/
			#$square = $this->selectEventTile();
			$sql = "SELECT sid, temp FROM map";
			$que = $this->db->prepare($sql);
			try {
				$que->execute();
				$array = [];
				while($row = $que->fetch(PDO::FETCH_ASSOC))
				{
					$rand = mt_rand(0,100);
					$days =mt_rand(1,TIME_CHOICE);

					/**********************************
					RAIN MATH
					For sake of round numbers, i'm going to assume every 'square' is a square mile or 640 acres.
					Average.
					The intensity of the storm ($ints) deterimines how much rain falls in one hour. 
					
					
					********************************/
					$dur = mt_rand(1,24);
					$ints = mt_rand(1,10);
					$rain = (($dur*$ints)*$days)*100;
					$rainCoun = 65;
					$pop = $this->getTilePop($row['sid']);
					$water = $this->WaterOnTile($row['sid']);
					//* If Temperature Exceeds Max, Do Other Functions
					if($row['temp'] >= (mt_rand(90,max_local_temp)))
					{	
						if($water <> 0)
						{
							/*
								
								Drought Functions -- May be expanded upon in seperate function at later date
								
							*/
							
							$dloss = abs($water/25); // Needs Tweaking, Originally was 100% of water as the cap for drought loss
							$evap = mt_rand(0,$dloss)*-1;
							$text = "[Enviro]Drought over {$row['sid']} | {$evap} IN lost | [TEMP] {$row['temp']} | [POP]{$pop}";
							$color = 'red';
							$this->UpdateTileWater($row['sid'],$evap);
						}
						else
						{
							// Square is out of water, go no further
							$text = "[Enviro]{$row['sid']} Has Dried Up; No Water Left | [TEMP] {$row['temp']} | [POP]{$pop}";
							$color = 'danger';
						}
						$this->updateTemperature($row['sid'], mt_rand(-5,5));
						$this->message($text,$color,30);
					}
					else
					{
						//* If Temperature within range, Rain Check (Rain Check Needs to be altered)
					if($rand <= $rainCoun)
					{
						if($row['temp'] <= 31)
						{
							// Do Something for Snow
							$color = 'blue';
							$text = "[Enviro] It Snowed on {$row['sid']} | [POP]{$pop}";
						}

						else
						{
							/********************************** 
							
							Actually Make it rain 
							
							***********************************/
							if($this->UpdateTileWater($row['sid'], $rain))
							{
								$text = "[Enviro]{$days} days and {$rain}in over {$row['sid']} | Chance $rand/$rainCoun | [TEMP]{$row['temp']} | [POP]{$pop}";
								$color = 'blue';
								$this->updateTemperature($row['sid'], mt_rand(-2,0));
							}
						}
						$this->message($text,$color,30);
					}
					else
					{
						if($water == 0)
						{
							
							$text = "[Enviro]{$row['sid']} Has Dried Up; No Water Left | [TEMP] {$row['temp']} | [POP]{$pop}";
							$color = 'danger';
						}
						else
						{
							$text = "[Enviro]No Rain Today over {$row['sid']} | Chance $rand/$rainCoun | [TEMP]{$row['temp']} | [POP]{$pop}";
							$color = 'red';
							$this->updateTemperature($row['sid'], mt_rand(0,3));
						}
						$this->message($text,$color,30);
					}
					}
					
					
				}
				return true;
			}catch(PDOException $e){ die($e->getMessage());}
	}
}