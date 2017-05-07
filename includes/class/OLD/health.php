<?php
class health extends basic_ai
{
	function getHealth($cid)
	{
		$sql = "SELECT health FROM citizens WHERE cid = :cid";
		$que = $this->db->prepare($sql);
		$que->bindParam(":cid", $cid);
		try { 
				$que->execute();
				$row = $que->fetch(PDO::FETCH_ASSOC);
				return $row['health'];
		}catch(PDOException $e) { echo $e->getMessage();}
	}
	function HealthCheck()
	{
		$this->cleanupwidows();

		$sql = "SELECT health, cid, thirst, hunger, air FROM citizens WHERE status > 0";
		$que = $this->db->prepare($sql);
		try { 
			$que->execute(); 
			while($row = $que->fetch(PDO::FETCH_ASSOC))
			{
				$this->killCitizens($row['cid'], $row['health'], $row['thirst'], $row['hunger'], $row['air']);
				$this->matchCitizens();
				$this->pregancyDeBuff();
				$this->healthyForPregancy();
				$this->replenisment();	
			}
			return $row['health'];
			}catch(PDOException $e) { echo $e->getMessage(); }
	}
	function intMath($h,$w)
	{
		return (($h+$w)/2);
		
	}
	function pregancyDeBuff()
	{
		$sql = "SELECT cid,pregnant_on FROM citizens WHERE status = 3";
		$que = $this->db->prepare($sql);
		$que->bindParam(':cid', $cid);
		try { 
			$que->execute(); 
			while($row = $que->fetch(PDO::FETCH_ASSOC))
			{
			if(!is_null($row['pregnant_on']))
			{
			$d1 = new DateTime($this->getTime());
			$d2 = new DateTime($row['pregnant_on']);
			
			$diff = $d2->diff($d1);
			if($diff->m >= 9)
			{
				$this->changeStatus($row['cid'], 2);
			}	
			}
			}
			} catch(PDOException $e) { echo $e->getMessage(); } 
	}
/* Run Check for Pregancy. if Status is 3, cit is preg or has been in the past 9 months */
	function healthyForPregancy()
	{
		$checker = $this->populationChecker();
		if($checker == 1)
		{
			$sql = "
					SELECT 
						h.health as hubhealth, 
						h.inti as hint,
						w.inti as wint,
						w.health as wealth, 
						w.cid as cid, 
						w.spouse_id as hid
					FROM 
						citizens as h, 
						citizens as w 
					WHERE 
					w.spouse_id = h.cid
					AND
					w.gender <> h.gender
					AND
					(h.status = 2 AND h.hunger > 45 AND h.thirst > 90 AND h.air > 90)
					AND
					(w.status = 2 AND w.hunger > 45 AND w.thirst > 90 AND w.air > 90)
					AND
					w.gender = 'f'";
			$que = $this->db->prepare($sql);
			try  {$que->execute(); 
				while($row = $que->fetch(PDO::FETCH_ASSOC))
				{
						$date = $this->getPregancyDate($row['cid']);	

						$d2 = new DateTime($this->getTime());
						$d1 = new DateTime($date);
						$diff = $d2->diff($d1);
						$int = $this->intMath($row['hint'],$row['wint']);
						$wage = $this->citizenAge($row['cid']);
						$hage = $this->citizenAge($row['hid']);
					if(($row['hubhealth'] >= 45) && ($row['wealth'] >= 45))
					{
						if($wage >= 18 && $hage >= 18)
						{
							#echo $diff->m."\n";
							if(is_null($date))
							{
								$this->newCitizens($row['cid'],$row['hid'], $int);
							}
							if(9 <= $diff->m )
							{
								$this->changeStatus($row['cid'], 2);
							}
							else
							{
								//* I'm pretty sure this does nothing, but I'm leaving it for fear that removal will destroy everything
							}
							
						}
					}
				}

			} catch(PDOException $e) { echo $e->getMessage(); } 	
		}
	}
	function cleanupwidows()
	{
		$sql = "UPDATE citizens as W, citizens as H set W.status = 1 WHERE H.status = 0 AND W.spouse_id = H.cid AND W.status <> 0";
		$this->db->exec($sql); 	
	}
function getPregancyDate($cid)
{
	$sql = "SELECT pregnant_on FROM citizens WHERE cid = :cid";
	$que = $this->db->prepare($sql);
	$que->bindParam(':cid', $cid);
	try { $que->execute();
		 $row = $que->fetch(PDO::FETCH_ASSOC);
		 return $row['pregnant_on'];
		}catch(PDOException $e) { }
}
/* Status Functions */
function changeStatus($cid, $status)
{
		$time = $this->getTime();
		$sql = "UPDATE citizens SET status = {$status}, pregnant_on = NULL WHERE cid = {$cid}";
		try { $this->db->exec($sql);}catch(PDOException $e) { echo $e->getMessage();}
		#echo "\e[1;10m Status Changed \e[0m \n";
}
function getStatus($cid)
{
	$sql = "SELECT status FROM citizens WHERE cid = :cid";
	$que = $this->db->prepare($sql);
	$que->bindParam(':cid', $cid);
	try { $que->execute();
		 $row = $que->fetch(PDO::FETCH_ASSOC);
		 return $row['status'];
		}catch(PDOException $e) { }
}
function updatePregancy($cid)
{
	$time = $this->getTime();
	$sql = "UPDATE citizens SET pregnant_on = '{$time}', status = '3' WHERE gender = 'f' AND cid = '{$cid}';";
	try { $this->db->exec($sql);}catch(PDOException $e) { echo $e->getMessage();}
}

/* Health Functions */
	function healthHit($cid, $health)
	{
	$sql ="	UPDATE 
				citizens 
				SET 
				health = health-{$health}
				WHERE cid = {$cid}
				AND
				STATUS >= 0
				AND
				(health-{$health} <= 0) OR (health-{$health} >= 125) ";
	try { $this->db->exec($sql);}catch(PDOException $e){ echo $e->getMessage();}
	}
	function healthTick($cid, $health)
	{
		$name = $this->getname($cid);
		if($this->citizenAge($cid) <= 25)
		{
		$thirst = ($this->citizenAge($cid)+1)/100;
		$hunger = ($this->citizenAge($cid)+1)/100;
		}
		else
		{
			$thirst = 1;
			$hunger = 1;
		}
		$air = abs(($this->citizenAge($cid)+1)/4);
		$sql = "UPDATE 
				citizens 
				SET 
				health = health-15, hunger = hunger-{$hunger}, thirst = thirst-{$thirst}, air = air-{$air} WHERE cid = {$cid} AND status >= 0";
		#echo $sql;
		if(extra_info == 1){
		echo  "\e[1;35m {$name['first_name']} {$name['last_name']} lost {$thirst} thirst & {$hunger} hunger & {$air} oxygen| \e[0m \n"; }
		try {$this->db->exec($sql);}catch(PDOException $e) { echo $e->getMessage(); } 
		//* Run Check AI
		$this->run_check();
	}
	function kill($cid, $reason)
	{
		$age = $this->citizenAge($cid);
		$name = $this->getname($cid);
		$sql = "UPDATE citizens SET status = 0, spouse_id = 0, died_on = :time WHERE cid = :cid";
		$que = $this->db->prepare($sql);
		$time = $this->getTime();
		$que->bindParam(':cid', $cid);
		$que->bindParam(':time', $time);
		try { $que->execute(); 
					
				echo "\e[1;31m {$name['first_name']} {$name['last_name']} died at {$age} of {$reason} | {$this->getTime()} \e[0m \n";	
		} catch(PDOException $e) { echo $e->getMessages(); } 
	}
	function killCitizens($cid, $health, $thirst, $hunger, $air)
	{
		$chance_of_death = mt_rand(0,4000);
		$age = $this->citizenAge($cid);
		$name = $this->getname($cid);
		if($chance_of_death <= 750 || $health <= 0)
		{
			$kill = mt_rand(0,1);
			if(($kill-$health) >= $health || $health <= 0)
			{
					$this->kill($cid, 'poor health');
			}
			elseif(100-$age <= mt_rand(0,50))
			{
				$this->kill($cid, 'old age');		

			}
			elseif($thirst <= -12)
			{
				$this->kill($cid, 'thirst');	
				
			}
			elseif($hunger <= -42)
			{
				$this->kill($cid, 'hunger');
			}
			elseif($air <= 0 )
			{
				$this->kill($cid, 'air');
				
			}
		}
		else
		{
			if(extra_info != '0')
			{
			switch(mt_rand(0,9))
			{
				case 1:
				$text ="{$name['first_name']} {$name['last_name']} fought back the reaper, Not Today Death!";
				break;
				case 2:
				$text = "Despite crippling odds, {$name['first_name']} {$name['last_name']} is still alive!";
				break;
				case 3:
				$text = "{$name['first_name']} {$name['last_name']} keeps on keeping on!";
				break;
				case 4: 
					$text = "{$name['first_name']} {$name['last_name']} beat Death with their own cold hand";
				break;
				case 5:
					$text = "{$name['first_name']} {$name['last_name']} lives to see another day";
				break;
				case 6:
					$text = "{$name['first_name']} {$name['last_name']} Beat Death in a game of chess!";
					break;
				case 7: 
					$text = "{$name['first_name']} {$name['last_name']} narrowly avoided The Reaper!";
					break;
				case 8: 
					$text = "{$name['first_name']} {$name['last_name']} had a dance off with Death and won!";
					break;
				case 9: 
					$text = "{$name['first_name']} {$name['last_name']} had a dance off with Death and won!";
					break;
				default:
				$text = NULL;
				break;
			}
			if($text == NULL)
			{
				
			}
			else
			{
			echo "\e[1;36m ".$text."Health:".$health." \e[0m \n";
			}
			}
			$this->healthTick($cid, mt_rand(0,3));

		}

}
}
