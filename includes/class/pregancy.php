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
				if(!$row)
				{
					// Do Nothing
				}
				else{
				$this->healthyForPregancy($row['cid'], $row['spouse_id'],$row['pregnant_on'], $row['relstat']);
				}
			}
			return true;
		} catch(PDOException $e) { echo $e->getMessage();}

	}
	function fertile($cid)
	{
	$sql=		
		"SELECT 
				genome
			FROM 
				genetics
			WHERE 
				cid = {$cid}
				";
		$que = $this->db->prepare($sql);
		
			try { 
				$que->execute(); 
				if($row = $que->fetch(PDO::FETCH_ASSOC))
				{
					$gen = $row['genome'];
					$gen = substr($gen,26,3);
					$age = $this->citizenAge($cid);
					$stay = 1;
					$fert = $age <= 25 ? $age/25 : $stay-($age*.009);
					if($gen == 'CAT')
					{
						//echo $cid." is infertile! \n";
						return 0;
					}
					if($age == 0)
					{
						return 0;
					}
					else
					{
						return 1;
					}
				}
			else
			{
				return 0;
			}
		}catch(PDOException $e) { die($e->getMessage());}
	}
	function healthyForPregancy($mom, $dad, $pDate,$stat)
	{
		/* 
			Rewriting this function because it was garbage
		*/
		$mname = $this->prettyName($mom); //* Prettfied Mom Name
		if($stat == 3) {
				$darray = array(
					'childbirth'=>array('min'=>15,'max'=>35),
					'stillborn'=>array('min'=>1,'max'=>45),
				);
				if($this->pregMath($pDate) >= 9){
					$death = mt_rand(0,300);
					switch($death)
					{
						case ($death >= 15 && $death <= 35):
							$this->kill($mom,'childbirth');
							break;
						case ($death >= 36 && $death <= 99):
							$this->message("[DEATH] {$mname}'s child was stillborn",'death',3);
							$this->statusChange($mom,2);
							break;
						default:
							$this->statusChange($mom,2);
							$int = $this->intMath($mom,$dad);
							#echo $mom."|".$dad."\n";
							$this->newCitizens($mom,$dad,$int);
							break;
					}
						
				}
		}
		else{
		$dname = $this->prettyName($dad); //* Prettfied Dad Name
			$mfert = $this->fertile($mom);
			$dfert = $this->fertile($dad);
			if($dfert && $mfert)
			{
				$this->statusChange($mom,3,1);
				$this->message("[LIFE]{$mname} is expecting!",'happy',3);
			}
			else {
				$text = '[INTERPERSONAL]';
				$array = array(
					"{$mname}({$mom}) AND {$dname}({$dad}) had SEX!",
					"{$dname}($dad) was in the mood but {$mname}({$mom}) had a headache",
					"{$mname}($mom) AND {$dname}($dad) fell asleep watching Home Alone 2",
					"{$mname}($mom) AND {$dname}($dad) did the nasty",
					"{$dname}($dad) gave {$mname}($mom) a bit of the 'how's yer father'",
					"{$dname} AND {$mname} were up all night talking",
					"{$dname} had a great time! {$mname} not so much",
					"{$dname} AND {$mname} were caught Shtupping"
				);
				shuffle($array);
				$text .= $array[0];
				$this->message($text,'green',3);
				return false;
			}
		}
	}
	function giveBirth($pDate,$mom)
	{
	}
	function newCitizens($mid,$fid,$int, $reset = NULL)
	{
		$ggp = $this->getParentsGenetics($mid,$fid);
		if(empty($ggp))
		{
			$mname = $this->prettyName($mid); //* Prettfied Mom Name
			$this->statusChange($mid,2);
			$this->message("[DEATH] {$mname}'s child was stillborn",'death',3);
		}
		else
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


			$genetics = $this->geneticMixer($ggp, true);
			$surname = $this->getName($fid);
			$madienName = $this->getName($mid);
			$surname = $this->surname_morpher($surname['last_name'], $madienName['last_name']);
			$gender = array('m','f');
			$gender = $gender[mt_rand(0,1)];
			$name = $this->newName($gender);
			$time = is_null($reset) ? $this->getTime() : '0982-00-00 00:00:00';
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
				$this->insertNewGenetics($genetics);
				$nName = $this->prettyName($mid);
				$dName = $this->prettyName($fid);
				$this->statusChange($mid,2);
				if(is_null($reset)){
					$text = "[LIFE]{$name} {$surname} was born on '{$time} to {$nName} & {$dName}!";}
				else{
					$text = "[LIFE]{$name} {$surname} was created by the blessed creator on {$time}";
				}
				$this->message($text,$gender,2);
				} catch(PDOException $e) { echo die('New Citizen Error: '.$e->getMessage()); } 
		}
	}

}