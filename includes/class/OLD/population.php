<?php
class population
{
	function populationChecker()
	{
		$pop = $this->totalPopulation();
		$supplies = $this->getSupplies();
		if(($supplies['food']+$supplies['water']) >= $pop*2)
		{
			return true;
		}
		else
		{
			return false;
		}
		
	}
	
}