<?php
class plants extends movement
{
	function killPlants()
	{
		$kill = mt_rand(0,4);
		$sql = "UPDATE map as m, supplies as s SET m.plants = m.plants-".$kill." 
			WHERE (m.temp > 100 OR m.water < 1 OR s.COTwo < 1) AND m.plants-{$kill} > 0";
		try { $this->db->exec($sql);}catch(PDOException $e) { die($e->getMessage());}
	}
	function getTilePlants($tid)
	{
		$sql = "SELECT plants FROM map WHERE sid = {$tid}";
		$que = $this->db->query($sql);
		try { $que->execute();
				$row = $que->fetch(PDO::FETCH_ASSOC);
			 return $row['plants'];
			}
		catch(PDOException $e){
			die($e->getMessage());
		}
	}
	function updatePlants($hgr,$tid)
	{
		$sql = "UPDATE map SET plants = plants-{$hgr}, seeds=seeds+".$hgr*20 ." WHERE sid = {$tid}";
		try { 
			$this->db->exec($sql);
			$this->PlantGrowth();
		}catch(PDOException $e){ die($e->getMessage());}
		
	}
	function transpirate()
	{
		$sql = "UPDATE map SET water = water+(plants/2)";
		try { $this->db->exec($sql);
						$this->killPlants();
			}catch(PDOexception $e){die($e->getMessage());}
	}
	function PlantGrowth()
	{
		if(mt_rand(1,5) == 5)
		{
			$sql = "UPDATE map SET plants = plants+(seeds/2), seeds=seeds/2  WHERE seeds > 0 AND fertile = 1";
			try { 
					$this->db->exec($sql);
					$this->transpirate();
				#die();
				}catch(PDOException $e) { die($e->getMessage());}
		}
	}
	
}