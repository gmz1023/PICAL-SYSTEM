<?php
class biofunctions extends death
{
	function breathe()
	{

	}
	function breathe2()
	{
		$pop = $this->totalPopulation();
		$wlP = $this->TotalWildlifePop();
		$co2 = ($pop*2)+($wlP*2)*TIME_CHOICE;
		/* This needs to be changed to suit the New Math;
			A new system needs to be implemented to improve the TIME_CHOICE changes
		*/
		$ox = (($pop * 50)*24)*TIME_CHOICE;
		$sql = "UPDATE 
					supplies
				SET 
					COTwo =COTwo+{$co2},
					air = air-{$co2}"; 
		try { 
			$this->db->exec($sql);
			}catch(PDOException $e){ die($e->getMessage());}
		$text = "[ENVIRO] {$co2} of CO2 AND {$ox}LBS of O2 ENTER THE ATMOSPHERE";
		$this->message($text,'info',30);
		$this->WildLifeReproduction();
		
	}
//* Probably should put this in it's own class at some point

}