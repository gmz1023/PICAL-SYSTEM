<?php
class enviroment extends plants
{

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
	function updateTemperature($sid,$temp)
	{
		$pop = $this->getTilePop($sid);
		$pop_temp_change = $pop/100;
		if(mt_rand(0,5) == 3)
		{
		$av = $this->selectAverageTemperature();
		$t = $this->Temperature($sid);
		if($av >= average_global_temp)
		{
			$temp = mt_rand(-10,-1);
		}
		if($av <= average_global_temp)
		{
			$temp = mt_rand(1,10);
		}
		else{
			if($t >= max_local_temp)
			{
			$temp = mt_rand(-30,0);
			}
			if($t <= min_local_temp)
			{
			$temp = mt_rand(0,25);
			}
		}
		$temp = $temp+$pop_temp_change;	
		$sql = "UPDATE map SET temp = temp+{$temp} WHERE sid = {$sid}";
		try { 
			$this->db->beginTransaction();
				$this->db->exec($sql);
			$this->db->commit();
		}
		catch(PDOException $e) { $this->db->rollback();}
		}
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
			$sql = "SELECT sid, temp FROM map";
			$que = $this->db->prepare($sql);
			try {
				$que->execute();
				$array = [];
				while($row = $que->fetch(PDO::FETCH_ASSOC))
				{
					$rand = mt_rand(0,100);
					$days = mt_rand(1,TIME_CHOICE);
					$rain = mt_rand(1,24)*$days;
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
							/* Actually Make it rain */
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