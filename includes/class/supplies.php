<?php
class supplies extends food
{
	function oxygenLevel()
	{
		$sql = "SELECT air FROM supplies";
		$que = $this->db->prepare($sql);
		try {
			$que->execute();
			$row = $que->fetch(PDO::FETCH_ASSOC);
			return $row['air'];
		}
		catch(PDOException $e)
		{}
	}
	function FoodSupplies()
	{
		$sql = "SELECT sum(water)+sum(plants) as food FROM map WHERE temp BETWEEN 31 AND 115";
		$que = $this->db->prepare($sql);
		try { $que->execute();
				$row = $que->fetch(PDO::FETCH_ASSOC);
			 return $row['food'];
			}
		catch(PDOException $e)
		{}
	}
	function updateSupplies()
	{
		if($this->loop % 1 == 0)
		{
			//* This ReWriting this function to the new system; Water and Food are regulated by teh MAP instead of random drops.
			$water = $this->ViableWaterReserves();
			$totalWater = $this->waterReserver();
			$food = $this->FoodSupplies();
			$ox = $this->oxygenLevel();
			$this->breathe();
			$text = "[WATER]{$water}/{$totalWater} | [AIR]{$ox} | [FOOD]{$food}";
			$this->message($text, 'info', 0);
		}
		else { //* No Supply Drop

		}
	}
	function ViableWaterReserves()
	{
		$sql = "SELECT sum(water) as water FROM map WHERE temp BETWEEN 31 AND 115";
		$que = $this->db->prepare($sql);
		try { $que->execute();
				$row = $que->fetch(PDO::FETCH_ASSOC);
			 return $row['water'];
			}
		catch(PDOException $e)
		{}
	}
	function waterReserver()
	{
		$sql = "SELECT sum(water) as water FROM map";
		$que = $this->db->prepare($sql);
		try { $que->execute();
				$row = $que->fetch(PDO::FETCH_ASSOC);
			 return $row['water'];
			}
		catch(PDOException $e)
		{}
	}
/**
		Plant Functions
			May need to be moved to a seperate Class -- Enviro or possibly new "Plants"
		
**/
	function breathe2()
	{
		/* Updates the Oxygen and CO2 levels */

	}
//* ReDoing Supplies Functions Here *//
	function update_specific_supply($s,$a)
	{
		$sql = "UPDATE supplies SET {$s} = {$s}+{$a}";
		try { $this->db->exec($sql);}catch(PDOException $e){ }
	}
}