<?php
class plants extends animals
{
	function killPlants()
	{
		$sql = "UPDATE 
					map as m,
					Atmosphere as a
				SET
					m.plants = m.plants-(m.plants/100-(a.oxygen/10))
				WHERE
					(a.oxygen < 20
					OR
					a.CoTwo < 0.04)
					AND
					((m.plants-(m.plants/100-(a.oxygen/10)))>0)
					
					";
		$sql2 = "UPDATE
					map
				 SET
				 	plants = plants-(abs(temp)*100),
					seeds = seeds-(abs(temp)*75)
				 WHERE
				 	temp > 94
					OR
					temp < 31
					AND
					((plants-(abs(temp)*100)) > 0
					AND
					(seeds-(abs(temp)*75)) > 0)
				 ";
		try { 
				#$this->db->exec($sql);
				#$this->db->exec($sql2);
		}
		catch(PDOException $e) { die($e->getMessage());}
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
	function plants_do_breath()
	{
		//* This Function is Borked so much that i need to rewrite it from stratch. so for now lets just do this
			$this->plantGrowth();
	}
	function transpirate()
	{
		$sql = "UPDATE map SET water = water+(plants/2) WHERE water+(plants/2) >= 0";
		try { $this->db->exec($sql);
						$this->randomSeedDrop();
			}catch(PDOexception $e){die("Plant Error(P48):".$e->getMessage());}
	}
	function randomSeedDrop()
	{
		if(mt_rand(0,1))
		{
		$sql = "
			UPDATE
				map
			SET
				seeds = seeds+".mt_rand(1,50)."";
		try { $this->db->exec($sql);
						$this->killPlants();
			}catch(PDOexception $e){die("Plant Error(P73):".$e->getMessage());}
		}
	}
	function plantGrowth()
	{
		/** 
			This Will Be Updated eventually to effect each square seperately
			Plant Growth needs to be modified to effect overall population growth as well.
		*/
		$pop = $pop = $this->totalPopulation();
		if(mt_rand(0,250) <= 200)
		{
		$sql = "UPDATE
					map as m,
					Atmosphere as a
				SET
					m.plants = (m.plants+(m.farm+1)),
					m.seeds=seeds/2,
					m.water = m.water-(m.plants*10),
					a.CoTwo = a.CoTwo - (m.plants/40000000),
					a.oxygen = a.oxygen+(m.plants/62607004)
				WHERE
					m.temp BETWEEN 20 and 100
                    AND m.water-(m.plants*10) > 0
					AND
					(m.plants+(m.seeds/2)*(m.farm+1)*(m.temp*0.8)) > 0;";
		try {
			#echo $sql; die();
			if($this->db->exec($sql))
			{
				
			}
			$this->transpirate();
			$c = $this->countPlants();
			$this->message("[Enviro]Plant Growth! [Number]{$c}",'blue',30);
		}catch(PDOException $e){die("Plant Error(P78):".$e->getMessage());}
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
			catch(PDOException $e){ die("Plant Error(P114):".$e->getMessage());}
	}
	//* 
}