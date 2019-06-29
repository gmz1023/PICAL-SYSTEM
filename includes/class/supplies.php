<?php
class supplies extends food
{
	function updateSupplies()
	{
		if($this->loop % 1 == 0)
		{
			//* This ReWriting this function to the new system; Water and Food are regulated by teh MAP instead of random drops.
			$water = $this->waterReserver();
			$this->breathe();
			$text = "[WATER]{$water} | [AIR] | [FOOD]";
			$this->message($text, 'green', 0);
		}
		else { //* No Supply Drop

		}
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
	function breathe()
	{
		/* Updates the Oxygen and CO2 levels */
		$pop = $this->totalPopulation();
		$co2 = $pop*10;
		$sql = "UPDATE 
					supplies
				SET 
					COTwo =COTwo+{$co2},
					air = air-{$co2}"; 
		try { 
			$this->db->exec($sql);
			}catch(PDOException $e){ die($e->getMessage());}
		$this->plants_do_breath();
	}
//* ReDoing Supplies Functions Here *//
	function update_specific_supply($s,$a)
	{
		$sql = "UPDATE supplies SET {$s} = {$s}+{$a}";
		try { $this->db->exec($sql);}catch(PDOException $e){ }
	}
}