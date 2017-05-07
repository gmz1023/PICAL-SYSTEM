<?php
class citizens extends health
{
	function totalPopulation()
	{
		$sql = "SELECT count(cid) as pop FROM citizens WHERE status  <> 0";
		$que = $this->db->prepare($sql);
		try { 
		$que->execute(); 
			$row = $que->fetch(PDO::FETCH_ASSOC);
			$population = $row['pop'];
		} catch(PDOException $e) { }
		return $population;
	}
	function overallPopulation()
	{
		$sql = "SELECT count(cid) as pop FROM citizens";
		$que = $this->db->prepare($sql);
		try { 
		$que->execute(); 
			$row = $que->fetch(PDO::FETCH_ASSOC);
			$population = $row['pop'];
		} catch(PDOException $e) { }
		return $population;	
	}
	function newName($gender)
	{
		$sql = "SELECT name FROM first_names WHERE gender = :gender ORDER BY rand() LIMIT 1";
		$que = $this->db->prepare($sql);
		$que->bindParam(":gender", $gender);
		try { 
			$que->execute();
			$row = $que->fetch(PDO::FETCH_ASSOC);
			return $row['name'];
		}catch(PDOException $e) { echo $e->getMessage();}
	
	}
/* Make the actual child */
	function newCitizens($mid,$fid,$int)
	{
		$sql = "INSERT INTO 
			`lurch`.`citizens` 
			(`first_name`, `last_name`, `gender`, `born_on`, `died_on`, `mother_id`, `father_id`, `status`, `spouse_id`,`inti`) 
			VALUES 
			(
				:name,
				:surname, 
				:gender, 
				:time, 
				NULL, 
				:mid, 
				:fid, 
				'1', 
				'0',
				:int);";
		
		$surname = $this->getname($fid);
		$surname = $surname['last_name'];
		$gender = array('m','f');
		$gender = $gender[mt_rand(0,1)];
		$name = $this->newName($gender);
		$time = $this->getTime();
		$que = $this->db->prepare($sql);
		$que->bindParam(':name', $name);
		$que->bindParam(':surname', $surname);
		$que->bindParam(':gender', $gender);
		$que->bindParam(':time', $time);
		$que->bindParam(':mid', $mid);
		$que->bindParam(':fid', $fid);
		$que->bindParam(':int', $int);
		try { 
			$que->execute(); 
			$nName = $this->getname($mid);
			$dName = $this->getname($fid);
			$nName = $nName['first_name'].' '.$nName['last_name'];
			$dName = $dName['first_name'].' '.$dName['last_name'];
			echo "\e[0;32m {$name} {$surname} was born on '{$time} to {$nName} & {$dName}! \e[0m \n";
			$this->updatePregancy($mid);
			} catch(PDOException $e) { echo $e->getMessage(); } 
	}

/* end of Child Making */
	function getParents($cid)
	{
		$sql = "SELECT mother_id as mom, father_id as dad FROM citizens WHERE cid = :cid";
		$que = $this->db->prepare($sql);
		$que->bindParam(":cid", $cid);
		try { 
			$que->execute();
			$row = $que->fetch(PDO::FETCH_ASSOC);

		}catch(PDOException $e) {}
	}
	function getname($cid)
	{
		$sql = "SELECT first_name, last_name FROM citizens WHERE cid = :cid";
		$que = $this->db->prepare($sql);
		$que->bindParam(':cid', $cid);
		try { 
			$que->execute(); 
			$row = $que->fetch(PDO::FETCH_ASSOC);
			} catch(PDOException $e) { echo $e->getMessage(); } 
		return $row;
	}
	function marryCitizens($cit1,$cit2)
	{
		$citpart = $this->getParents($cit1);
		$citpart2 = $this->getParents($cit2);
		if(($citpart2['dad'] <> $citpart['dad']) or ($citpart2['dad'] == 0 && $citpart['dad'] == 0))
		{
		if(($cit1 <> $cit2))
		{
			$cit1age = $this->citizenAge($cit1);
			$cit2age = $this->citizenAge($cit2);
			if(($cit1age >= 18) && ($cit2age >= 18))
			{
		$sql = "UPDATE citizens SET status = 2 WHERE cid IN('".$cit1."','".$cit2."'); ";
		$sql .="UPDATE citizens SET spouse_id = '".$cit1."' WHERE cid = '".$cit2."'; ";
		$sql .= "UPDATE citizens SET spouse_id = '".$cit2."' WHERE cid = '".$cit1."'";
		try { $this->db->exec($sql); }catch(PDOexception $e) { echo $e->getMessage(); }
		$name1 = $this->getname($cit1);
		$name2 = $this->getname($cit2);
		echo "\e[0;32m {$name1['first_name']} {$name1['last_name']} AND {$name2['first_name']} {$name2['last_name']} married on {$this->getTime()}! \e[0m \n";
		}
		}
		}
	}
	function matchCitizens()
	{
		$singles = $this->getSingleCitizens();
		$count = count($singles) ;
		$count = $count-1;
		switch($count)
		{
			case $count<2:
			//donothingnow
			break;
			case $count>=2:
			$cit1 = $singles[mt_rand(0,$count)]['cid'];
			$cit2 = $singles[mt_rand(0,$count)]['cid'];
			$this->marryCitizens($cit1,$cit2);
			break;	
		}

	}
	function citizenGender($cid)
	{
		$sql = "SELECT gender FROM citizens WHERE cid = :cid";
		$que = $this->db->prepare($sql);
		$que->bindParam(':cid', $cid);
		try { 
			$que->execute(); 
			$row = $que->fetch(PDO::FETCH_ASSOC);
		}catch(PDOException $e) {}
	}
	function citizenAge($cid)
	{
		$sql = "SELECT born_on FROM citizens WHERE cid = :cid";
		$que = $this->db->prepare($sql);
		$que->bindParam(':cid', $cid);
		try { 
			$que->execute(); 
			$row = $que->fetch(PDO::FETCH_ASSOC);
			$d1 = new DateTime($this->getTime());
			$d2 = new DateTime($row['born_on']);
			
			$diff = $d2->diff($d1);
			
			return $diff->y;
			} catch(PDOException $e) { echo $e->getMessage(); } 		
	}
	function getSingleCitizens()
	{
		$sql = "SELECT 
					cid,
					gender as g
				FROM
					citizens
				WHERE
					status = '1';";
		$que = $this->db->prepare($sql);
		$single = [];
		try { $que->execute(); 
		while($row = $que->fetch(PDO::FETCH_ASSOC))
		{
			$single[] = array('cid'=>$row['cid'], 'g'=>$row['g']); 	
		}
		}catch(PDOException $e) { echo $e->getMessage(); }
		return $single;
	}
}