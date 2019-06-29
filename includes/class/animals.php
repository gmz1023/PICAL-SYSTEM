<?php
class animals extends movement
{
	function TotalWildlifePop()
	{
		$sql = "SELECT sum(wildlife) as wl FROM map";
		$que = $this->db->prepare($sql);
		try {
			$que->execute();
			$row = $que->fetch(PDO::FETCH_ASSOC);
			return $row['wl'];
		}catch(PDOException $e) { die('Wild life census');}
	}
	function animals_eat_to()
	{
		$sql = "UPDATE map
				SET plants = CASE
				WHEN plants-wildlife > 1 THEN plants-wildlife ELSE plants END,
				";
		try { $this->db->exec($sql);}catch(PDOException $e) { //die( $e->getMessage());
		}
	}
	function WildLifeReproduction()
	{
		if(mt_rand(0,10) == 1)
		{
		$sql = "
			UPDATE 
				map
			SET
				wildlife = wildlife*".mt_rand(4,10)." WHERE wildlife <> 0 AND plants <> 0";
		
		//* This will have to be updated eventually to allow animals to die off as well
		try { 
			$this->db->exec($sql);
			$pop = $this->TotalWildlifePop();
			$this->message("[Wild Life]Animal Growth | {$pop}", 'blue',30);
			}
		catch(PDOException $e) {}
		}
		else
		{
			$this->message("[Wild Life] No Animal Growth", 'blue',30);
			
		}
		$this->animals_eat_to();
	}
	function husbandry($tid,$e)
	{
		$e = $e*TIME_CHOICE;
		$sql = "UPDATE map SET wildlife = (wildlife+{$e}*(ranch+1)), ranch = ranch+1 WHERE sid = {$tid}";
		try {
			if($this->db->exec($sql))
			{
				$text = "[FARMING] RANCH STARTED {$e} ANIMALS RAISED";
				$this->message($text,'green',30);
			}
		
		}catch(PDOException $e) { echo die($e->getMessage());}
	}
}