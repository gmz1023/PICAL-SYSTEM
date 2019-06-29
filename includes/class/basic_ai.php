<?php
class basic_ai extends food
{
	function run_check()
	{

		$sql = "SELECT cid, thirst, hunger, health, air FROM citizens WHERE status = 1";
		$que = $this->db->prepare($sql);
		try { 
			if($que->execute())
			{
			while(($row = $que->fetch(PDO::FETCH_ASSOC)) && ($this->totalPopulation() >= 0))
			{
				$cid = $row['cid'];
				$name = $this->getname($cid);


			}
			}
			else
			{
				echo "ERROR";
			}
		} catch(PDOException $e) { echo $e->getMessage();
									 	}
		
	}

/* The Fight Song */
	function getRandomCitizen()
	{
		$sql = "SELECT cid FROM citizens WHERE status <> 0 ORDER BY rand() LIMIT 1";
		$que = $this->db->prepare($sql);
		try {
				$que->execute();
				$row = $que->fetch(PDO::FETCH_ASSOC);
			return $row['cid'];
		}
		
			
		catch(PDOException $e) { echo $e->getMessage();}
	}
	function fight()
	{
		$cid1 = $this->getRandomCitizen();
		$cid2 = $this->getRandomCitizen();
		if($cid1 <> $cid2)
		{
			if(($this->citizenAge($cid1) >= 8) && ($this->citizenAge($cid2) >= 8))
			{
			$this->fightSong($cid1,$cid2);
			}
		}
	}
	function fightSong($cid1, $cid2)
	{
		$cit1Health = $this->getHealth($cid1);
		$cit2Health = $this->getHealth($cid2);
		$hit1 = mt_rand(0,45);
		$hit2 = mt_rand(0,45);
		if(($cit1Health - $hit1) <= 0)
		{
			$this->kill($cid1, 'fight');
		}
		if(($cit2Health - $hit1) <= 0)
		{
			$this->kill($cid2, 'fight');
		}
		if(extra_info == '1')
		{
			$name1 = $this->getname($cid1);
			$name2 = $this->getname($cid2);
		echo  "\e[1;33m {$name1['first_name']} {$name1['last_name']} got in a fight with {$name2['first_name']} {$name2['last_name']} \e[0m \n";
		}
		$this->healthHit($cid2, $hit2);
		$this->healthHit($cid1, $hit1);

	}

}