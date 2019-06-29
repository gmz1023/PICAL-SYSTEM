<?php
class citizens extends family
{
	/************************************************
	
		Anything to do with Genders should go here
			or should i say "Sex" to not offend people? 
			This section is deticated to the X and Y chromosone
	
	*************************************************/
	function getGender($cid)
	{
		$sql = "SELECT gender FROM citizens WHERE cid = {$cid}";
		$que = $this->db->prepare($sql);
		try { 
				$que->execute();
				$row = $que->fetch(PDO::FETCH_ASSOC);
				return $row['gender'];
		}catch(PDOException $e) { echo $e->getMessage(); }
	}
	/************************************************
	
	
		Status Functions 
	
	************************************************/
	function statusChange($cid,$status, $preg=NULL)
	{
		$time = $this->getTime();
		$sql = "UPDATE citizens SET relstat = '{$status}' WHERE cid = {$cid};";
		$sql2 = "UPDATE citizens SET pregnant_on = '{$time}' WHERE cid = {$cid};";
		switch($preg)
		{
			case is_null($preg):
				try { 
					$this->db->beginTransaction();
					$this->db->exec($sql);
					$this->db->commit();
				}catch(PDOException $e) { echo $e->getMessage();}
				break;
			case !is_null($preg):
				try { 
					$this->db->beginTransaction();
					$this->db->exec($sql);
					$this->db->exec($sql2);
					$this->db->commit();
				}catch(PDOException $e) { echo $e->getMessage();}
				break;
		}
		

	}
//* Why Wasn't This A Function Already
	function checkStatus($cid)
	{
		$sql = "SELECT status FROM citizens WHERE cid = {$cid}";
		$que = $this->db->prepare($sql);
		try { 
				$que->execute();
				$row = $que->fetch(PDO::FETCH_ASSOC);
			return $row['status'];
			}
		catch(PDOException $e)
		{
			die($e->getMessage());	
		}
	}
	/************************************************
	
		Intelligence Functions
	
	*************************************************/
	function getInteli($cid)
	{
		$sql = "SELECT inti as iq FROM citizens WHERE cid = '{$cid}' LIMIT 1";
		$que = $this->db->prepare($sql);
		try { 
				$que->execute();
				$array = [];
				$row = $que->fetch(PDO::FETCH_ASSOC);
					return $row['iq'];
		}catch(PDOException $e) { echo $e->getMessage();}

	}
	/************************************************
	
		Anything to do with names
	
	************************************************/
	function getName($cid)
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
	function prettyName($cid)
	{
		$name = $this->getName($cid);
		
		return $name['first_name'].' '.$name['last_name'];
		
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
	function marriedName($cit, $lastname)
	{
		$sql = "UPDATE citizens SET last_name = '{$lastname}' WHERE cid = {$cit}";
		try { $this->db->exec($sql);}catch(PDOException $e) { echo $e->getMessage(); }
	}
	function surname_morpher($fname, $mname)
	{
		/* Need to figure out how to make this work how I want it to work -- 
			Ideally it'll merge to names like Eiffel and Lovelace into Eiffelace or something
		*/
		if(is_null($fname))
		{
			$lname = $mname;
		}
		else
		{
			$lname = $fname;
		}
		return $lname;
		
	}
	/************************************************
		
		
			"Relationship Goals" 
			Fuck Marry Kill 
		
		
		******************************************/
	function interpersonal($cit1,$cit2,$action)
	{
		$sql = "INSERT INTO relationship (cit1,cit2,value) VALUES ({$cit1}, {$cit2}, 1) ON DUPLICATE KEY UPDATE weight = weight+{$action}";
		try { $this->db->exec($sql);}catch(PDOException $e) { echo $e->getMessage();}
		
	}
	/*
		marryCitizens and matchCitizens are OLD FUNCTIONS -- they are not DEFUNC but need to be retooled for new functions 
		it needs to be edited to include the new "Relationship Goals" weights from above -- IE if someone hates someone, they shouldn't marry them... I know that's unrealistic but hey! This is a simulation! 
	*/
}