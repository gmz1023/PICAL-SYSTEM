<?php
class simulation extends map
{
	function do_run2()
	{
		
	}
	function do_run()
	{
		#die("Something Went Wrong!!!!");
		#$this->updateSupplies();
		$this->Rain();
		if($this->virusCount() == 0)
		{
			$this->createVirus();
		}
		if($this->updateTime())
		{
			
			if($this->marryCitizens())
			{
				
				if($this->getHealthyCouples())
				{
					
				
					$this->run_the_guanlet();
				}
				else
				{
					die("Something Went Wrong! Get Healthy Couples; ");
					exit;
				}
			}
			else
			{
				/* This needs to be fixed in the marryCitizens function -- it needs to return true somewhere where it's not */
				#die("Something Went Wrong! Marriage-SIM.php");
			}
		}
		else
		{die("Something Went Wrong! Update Time!");}
	}
	
}