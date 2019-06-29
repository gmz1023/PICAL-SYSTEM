<?php
class enviroment extends plants
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
			}
			$this->Rain();
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
	function updateTemperature($sid,$temp)
	{
		$av = $this->selectAverageTemperature();
		$t = $this->Temperature($sid);
		if($av >= average_global_temp)
		{
			$temp = mt_rand(-10,-1);
		}
		elseif($av <= average_global_temp)
		{
			$temp = mt_rand(1,10);
		}
		else{
			if($t <= max_local_temp)
			{
			$temp = mt_rand(-10,0);
			}
			if($t >= min_local_temp)
			{
			$temp = mt_rand(0,15);
			}
		}		
		$sql = "UPDATE map SET temp = temp+{$temp} WHERE sid = {$sid}";
		try { 
			$this->db->beginTransaction();
				$this->db->exec($sql);
			$this->db->commit();
		}
		catch(PDOException $e) { $this->db->rollback();}
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
			#$square = $this->selectEventTile();
			$sql = "SELECT sid, temp FROM map ORDER BY rand()";
			$que = $this->db->prepare($sql);
			try {
				$que->execute();
				$array = [];
				while($row = $que->fetch(PDO::FETCH_ASSOC))
				{
					$rand = mt_rand(0,100);
					$rain = mt_rand(1,24);
					$rainCoun = mt_rand(0,100);
					$water = $this->WaterOnTile($row['sid']);
					if($row['temp'] >= (100+mt_rand(-15,15)))
					{
						
						if($water <> 0)
						{
							/*
								
								Drought Functions -- May be expanded upon in seperate function at later date
								
							*/
							
							$dloss = abs($water/25); // Needs Tweaking, Originally was 100% of water as the cap for drought loss
							$evap = mt_rand(0,$dloss)*-1;
							$text = "[Enviro]Drought over {$row['sid']} | {$evap} IN lost | [TEMP] {$row['temp']}";
							$color = 'red';
							$this->UpdateTileWater($row['sid'],$evap);
						}
						else
						{
							$text = "[Enviro]{$row['sid']} Has Dried Up; No Water Left | [TEMP] {$row['temp']}";
							$color = 'danger';
						}
						$this->updateTemperature($row['sid'], mt_rand(-5,5));
						$this->message($text,$color,30);
					}
					else
					{
					if($rand <= $rainCoun)
					{
						if($row['temp'] <= 31)
						{
							// Do Something for Snow
							$color = 'blue';
							$text = "[Enviro] It Snowed on {$row['sid']}";
						}

						else
						{
							if($this->UpdateTileWater($row['sid'], $rain))
							{
								$text = "[Enviro] it rained {$rain}IN on {$row['sid']} | Chance $rand/$rainCoun! [TEMP]{$row['temp']}";
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
							$text = "[Enviro]{$row['sid']} Has Dried Up; No Water Left | [TEMP] {$row['temp']}";
							$color = 'danger';
						}
						else
						{
							$text = "[Enviro]No Rain Today over {$row['sid']} | Chance $rand/$rainCoun | [TEMP]{$row['temp']}";
							$color = 'red';
							$this->updateTemperature($row['sid'], mt_rand(0,3));
						}
						$this->message($text,$color,30);
					}
					}
					
				}
			}catch(PDOException $e){}
	}
}