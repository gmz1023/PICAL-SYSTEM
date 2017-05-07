<?php
class maths extends simulation
{
	//* Statistics stuff*/
	function getStatCount($stat)
	{
		$sql = "SELECT count(*) as tot FROM citizens WHERE relstat = {$stat}";
		$que = $this->db->prepare($sql);
		try { 
			$que->execute();
			$row = $que->fetch(PDO::FETCH_ASSOC);
			return $row['tot'];
			
			}catch(PDOException $e) { die($e->getMessage());}
	}
	function leadinCOD()
	{
		$sql = "SELECT cod FROM citizens WHERE relstat = 0 GROUP BY cod ORDER BY count(cod) DESC LIMIT 1;";
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
		$ts = TIME_CHOICE." days";
		$preg = $this->getStatCount(3);
		$dead = $this->getStatCount(0);
		$cod = $this->leadinCOD();
		$LS = $this->successfulName();
	$sql = "INSERT INTO stats(time_step,it,cycle,pop, pregnant,dead, topDeathCause,lastName,sucessors) values ('{$ts}','{$_SESSION['it']}','{$_SESSION['loop']}', '{$this->population}', {$preg},{$dead}, '{$cod}','{$LS['LS']}','{$LS['LST']}');";
	try { 
		
		if($this->db->exec($sql))
		{
			return true;
		}}catch(PDOException $e) { die($e->getMessage());}
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
		$sql = "SELECT count(cid) as pop FROM citizens WHERE infected  <> 0 AND relstat <> 0";
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
		$sql = "SELECT count(cid) as pop FROM citizens WHERE relstat  <> 0";
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
		$h = $this->getInteli($h);
		$w = $this->getInteli($w);
		return (($h+$w)/2);
		
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
			$d1 = new DateTime($this->getTime());
			$d2 = new DateTime($row['born_on']);
			
			$diff = $d2->diff($d1);
			
			return $diff->y;
			} catch(PDOException $e) { echo $e->getMessage(); } 		
	}
}