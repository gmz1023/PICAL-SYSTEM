<?php
class pregancy extends tribe
{
	function getHealthyCouples()
	{
		$sql = "SELECT cid,spouse_id,pregnant_on,relstat FROM citizens WHERE gender = 'f' AND relstat >= 2 AND status = 1";
		$que = $this->db->prepare($sql);
		try { 
			$que->execute();
			while($row = $que->fetch(PDO::FETCH_ASSOC))
			{
				$this->healthyForPregancy($row['cid'], $row['spouse_id'],$row['pregnant_on'], $row['relstat']);
			}
			return true;
		} catch(PDOException $e) { echo $e->getMessage();}
		#echo "Run!";
	}
	function fertile($cid)
	{
	$sql=		"SELECT genetics.infert1 AS gert,
				    virus.infert1 AS vert
			FROM citizens
			JOIN genetics ON citizens.cid = genetics.cid
			LEFT JOIN virus ON virus.vid = citizens.infected
			WHERE 
				citizens.cid = {$cid}
				";
		$que = $this->db->prepare($sql);
		try { 
			$que->execute(); 
			$row = $que->fetch(PDO::FETCH_ASSOC);
			if(array_sum($row) > 0) { return false; } else { return true;} return $row['infert1'];}catch(PDOException $e) { echo $e->getMessage();}
	}

	function healthyForPregancy($mom, $dad, $pdate,$stat)
	{
							$mname = $this->prettyName($mom);
					$dname = $this->prettyName($dad);
		if($stat == 3)
		{
			if(mt_rand(0,900) == 900)
			{
				$this->kill($mom, 'childbirth');
			}
			elseif(mt_rand(0,99999999) == 9)
			{
				/* Probably not rare enough but essentially a dad flips shit. Murder Suicide. I'll probably be adding more dark things here */
				#$this->kill($mom, 'rampage');
				#$this->kill($dad, 'rampage');
			}
			else
			{
			if($this->pregMath($pdate) >= 9)
			{
				$this->statusChange($mom,2);
				#echo "Pregancy Chance:".$mom_chance."/".$dad_chance."Mom: {$mom_age} Dad: {$dad_age}\n";
				$int = $this->intMath($mom,$dad);
				#echo $mom."|".$dad."\n";
				$this->newCitizens($mom,$dad,$int);
			}
			}
		}
		else{
			$mom_age = $this->citizenAge($mom);
		$dad_age = $this->citizenAge($dad);
			$bothAge = $mom_age+$dad_age;
			$dadFert = $this->fertile($dad);
			$momFert = $this->fertile($mom);
			if($dadFert && $momFert)
			{
				if(mt_rand(0,$bothAge) <= mt_rand(0,18))
				{
						
					$this->statusChange($mom,3,1);
					$this->message("[PREGNANT]{$mname} is expecting!",'happy','3');
				}
				else
				{

					$text = "[INTERPERSONAL]";
					switch(mt_rand(1,10))
					{
						case 1:
							$text .= "{$mname}($mom) AND {$dname}($dad) had sex";
							break;
						case 2:
							$text .= "{$dname}($dad) was in the mood but {$mname}({$mom}) had a headache";
							break;
						case 3:
							$text .= "{$mname}($mom) AND {$dname}($dad) fell asleep watching Home Alone 2";
							break;
						default:
							$text .= "{$dname} AND {$mname} were up all night talking";
							break;
					}
					
					$this->message($text,'green','3');
					return false;
				}
			}
			else
			{
				$mname = $this->prettyName($mom);
				$dname = $this->prettyName($dad);
				$this->message("{$mname} & {$dname} infertile!", 'red', '2');
			}
		}
	}
	function newCitizens($mid,$fid,$int)
	{
		$sql = "INSERT INTO 
			citizens
			(
				first_name, 
				last_name, 
				gender, 
				born_on, 
				died_on, 
				mother_id, 
				father_id, 
				relstat, 
				spouse_id,
				inti,
				tile_id
				) 
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
				:int,
				:tile
			);";
		$getParentGenetic = $this->getParentsGenetics($mid,$fid);
		$surname = $this->getName($fid);
		$madienName = $this->getName($mid);
		$surname = $this->surname_morpher($surname['last_name'], $madienName['last_name']);
		$gender = array('m','f');
		$gender = $gender[mt_rand(0,1)];
		$name = $this->newName($gender);
		$time = $this->getTime();
		$que = $this->db->prepare($sql);
		$int = $int+mt_rand(-3,3);
		$mtile = $this->getCitizenTile($mid);
		$que->bindParam(':name', $name);
		$que->bindParam(':surname', $surname);
		$que->bindParam(':gender', $gender);
		$que->bindParam(':time', $time);
		$que->bindParam(':mid', $mid);
		$que->bindParam(':fid', $fid);
		$que->bindParam(':int', $int);
		$que->bindPAram(':tile', $mtile);
		try { 
			$que->execute(); 
			$this->insertNewGenetics($getParentGenetic);
			$nName = $this->prettyName($mid);
			$dName = $this->prettyName($fid);
			$this->statusChange($mid,2);
			$text = "[LIFE]{$name} {$surname} was born on '{$time} to {$nName} & {$dName}!";
			$this->message($text,$gender,2);
			} catch(PDOException $e) { echo die('New Citizen Error: '.$e->getMessage()); } 
	}

}