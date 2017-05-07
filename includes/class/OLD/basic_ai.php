<?php
class basic_ai extends food
{
	function getSupplyCount()
	{
		$sql = "SELECT * FROM supplies";
		$que = $this->db->prepare($sql);
		try { $que->execute();
			$row =	$que->fetch(PDO::FETCH_ASSOC);
			 return $row; 
			}catch(PDOException $e) { } 
		
	}
	function getNeed($need, $value, $cid)
	{
		$value = abs($value);
				$name = $this->getname($cid);		
		switch($need)
		{
			case 'thirst':
			$resource = 'water';
			break;
			case 'hunger':
			$resource = 'food';
			break;
			case 'health':
			$resource = 'medicine';
			break;	
			case "air":
			$resource = "air";
			break;
			case 'cid':
			$resource = '';
			break;

		}
		$sql = "UPDATE 
				citizens, 
				supplies 
				SET 
					citizens.{$need} = citizens.{$need}+(100-{$value}), 
					supplies.{$resource} = supplies.{$resource}-({$value}) 
					WHERE (supplies.{$resource}-(100-{$value}) > 0) AND (citizens.{$need} < 100) AND supplies.{$resource} >= 0 AND status >= 1 AND cid = {$cid};";
		try { 
		if($this->db->exec($sql))
		{
			$sup = $this->getSupplyCount();
			if(extra_info == true){
			echo "\e[1;35m {$name['first_name']} {$name['last_name']} consumes {$resource} | \e[0m \n";
			}
		}
		else
		{
			echo $sql;
			exit;
			$age = $this->citizenAge($cid);
			$text = "\e[0;31m {$name['first_name']} {$name['last_name']} went another day without {$resource} | age: {$age} \e[0m \n";  
			echo $text;	
			$this->healthHit($cid, mt_rand(0.999,1.999));
		}
		}catch(PDOException $e) { echo $e->getMessage(); } 
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