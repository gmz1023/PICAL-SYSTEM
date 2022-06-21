<?php
class maths extends simulation
{
	//* Statistics stuff*/
	function getGenderRatio()
	{
		$sql = "SELECT 
					sum(case when `gender` = 'm' then 1 else 0 end)/count(*) as male_ratio,
				   	sum(case when `gender` = 'f' then 1 else 0 end)/count(*) as female_ratio
				FROM 
					citizens
				WHERE
					status > 0
		";
		$que = $this->db->prepare($sql);
		try { 
			$que->execute();
			$row = $que->fetch(PDO::FETCH_ASSOC);
			return 'M Ratio:'.$row['male_ratio'].'| F Ratio:'.$row['female_ratio'];
		}catch(PDOException $e) { die($e->getMessage());}
	}
	function getStatCount($stat)
	{
		$sql = "SELECT count(*) as tot FROM citizens WHERE status = {$stat}";
		$que = $this->db->prepare($sql);
		try { 
			$que->execute();
			$row = $que->fetch(PDO::FETCH_ASSOC);
			return $row['tot'];
			
			}catch(PDOException $e) { die($e->getMessage());}
	}
	function leadinCOD()
	{
		$sql = "SELECT cod FROM citizens WHERE status = -1 GROUP BY cod ORDER BY count(cod) DESC LIMIT 1;";
		$que = $this->db->prepare($sql);
		try { 
			$que->execute();
			$row = $que->fetch(PDO::FETCH_ASSOC);
			return $row['cod'];
			
			}catch(PDOException $e) { die($e->getMessage());}
	}
	function successfulName()
	{
		$sql = "SELECT last_name as LS, count(*) as LST FROM citizens GROUP BY last_name ORDER BY count(*) DESC LIMIT 1";
		$que = $this->db->prepare($sql);
		try { 
			$que->execute();
			$row = $que->fetch(PDO::FETCH_ASSOC);
			return $row;
			
			}catch(PDOException $e) { die($e->getMessage());}
	}
	function stats()
	{
		//* This should be made into a single, giant, SQL query.
		$ts = TIME_CHOICE." days";
		$preg = $this->getStatCount(3);
		$dead = $this->getStatCount(-1);
		$plants = $this->countPlants();
		$cod = $this->leadinCOD();
		$ox = 100; // * needs to be fixed
		$LS = $this->successfulName();
		$wa = 0;//$this->ViableWaterReserves();
		$wl = $this->TotalWildlifePop();
		$avTemp = $this->selectAverageTemperature();
	$sql = "INSERT INTO 
				stats
				(
				time_step,
				it,cycle,
				pop, 
				pregnant,
				dead, 
				topDeathCause,
				last_name,
				sucessors,
				plants,
				air,
				water,
				wildlife,
				avgTemp
				) 
				values 
				('{$ts}',
				'{$_SESSION['it']}',
				'{$_SESSION['loop']}', 
				'{$this->population}', 
				{$preg},
				{$dead}, 
				'{$cod}',
				'{$LS['LS']}',
				'{$LS['LST']}',
				'{$plants}',
				'{$ox}',
				'{$wa}',
				'{$wl}',
				'{$avTemp}'
				);";
	try { 
		if($this->db->exec($sql))
		{
			return true;
		}}catch(PDOException $e) { 
		echo $sql;
		
		die($e->getMessage());}
	}
	/* Unrelated */
	function formatBytes($bytes, $precision = 2) { 
    $units = array('B', 'KB', 'MB', 'GB', 'TB'); 

    $bytes = max($bytes, 0); 
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
    $pow = min($pow, count($units) - 1); 

    // Uncomment one of the following alternatives
    // $bytes /= pow(1024, $pow);
    // $bytes /= (1 << (10 * $pow)); 

    return round($bytes, $precision) . ' ' . $units[$pow]; 
} 
	/* This class is simply here to seperate all the Count/Average/ETC functions from the rest of the classes.
	*/
	
	function selectInfectedPop()
	{
		/* Used for Info and basic functions */
		$sql = "SELECT count(cid) as pop FROM citizens WHERE infected  <> 0 AND status = 1";
		$que = $this->db->prepare($sql);
		try { 
		$que->execute(); 
			$row = $que->fetch(PDO::FETCH_ASSOC);
			$population = $row['pop'];
		} catch(PDOException $e) { echo $e->getMessage(); }
		return $population;
	}
	function countOffspring($cid)
	{
		/* This function is used to limit Baby Booms */
		$sql = "SELECT count(cid) as offspring FROM citizens WHERE mother_id = {$cid}";
		$que = $this->db->prepare($sql);
		try { 
			$que->execute(); 
			$row = $que->fetch(PDO::FETCH_ASSOC);
			return $row['offspring'];
		}catch(PDOException $e) { echo $e->getMessage();}
	}
	function totalPopulation()
	{
		/* Used for Info and basic functions */
		$sql = "SELECT count(cid) as pop FROM citizens WHERE status = 1";
		$que = $this->db->prepare($sql);
		try { 
		$que->execute(); 
			$row = $que->fetch(PDO::FETCH_ASSOC);
			$population = $row['pop'];
		} catch(PDOException $e) { echo $e->getMessage(); }
		return $population;
	}
	function overallPopulation()
	{
		/* Used for Info and basic functions */
		$sql = "SELECT count(cid) as pop FROM citizens";
		$que = $this->db->prepare($sql);
		try { 
		$que->execute(); 
			$row = $que->fetch(PDO::FETCH_ASSOC);
			$population = $row['pop'];
		} catch(PDOException $e) { echo $e->getMessage(); }
		return $population;	
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
	/* Average / Count Functions for Citizens */
	function averageIQ()
	{
		/* Used for Info and basic functions */
		$sql = "SELECT avg(inti) as iq FROM citizens LIMIT 1";
		$que = $this->db->prepare($sql);
		try { $que->execute(); $row = $que->fetch(PDO::FETCH_ASSOC); return $row['iq'];}catch(PDOException $e) { }
	}
	function intMath($h,$w)
	{
		//* There needs to be a genetic component to this too -- but that means the Genetics system must be flushed out.
		$h = $this->getInteli($h);
		$w = $this->getInteli($w);
		$int = mt_rand(65,($h+$w));
		$int = $int < 165 ? $int :  $int-mt_rand(0,$int);
		$int = $int < 65 ? 65 : $int; 
		return $int;
		
	}
	/**************
	
		Pregnacy Math
	
	***************/
	function pregMath($preg_on)
	{
			$d1 = new DateTime($this->getTime());
			$d2 = new DateTime($preg_on);
			$diff = $d2->diff($d1);
			return $diff->m;
	}
	/**************
		CITIZEN AGE FUNCTION 
	*****************/
	function citizenAge($cid)
	{
		$sql = "SELECT born_on FROM citizens WHERE cid = :cid";
		$que = $this->db->prepare($sql);
		$que->bindParam(':cid', $cid);
		try { 
			$que->execute(); 
			$row = $que->fetch(PDO::FETCH_ASSOC);

			if(!$row)
			{
				return '0';
			}
			else
			{
			$d1 = new DateTime($this->getTime());
			$d2 = new DateTime($row['born_on']);
			
			$diff = $d2->diff($d1);
			
			return $diff->y;
			}
			} catch(PDOException $e) { echo $e->getMessage(); } 		
	}
}