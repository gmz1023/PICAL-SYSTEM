<?php
class family extends pregancy
{
	/*
	
		Manages Families 
			Disallows Incest (Probably going to be opition?)
			Manages Genetic Disorders (AIDS, Cancers, ETC)
			
	*/
	function getSpouse($cid)
	{
		$sql = "SELECT cid FROM citizens WHERE spouse_id = {$cid}";
		$que = $this->db->prepare($sql);
		try { $que->execute();
				$row = $que->fetch(PDO::FETCH_ASSOC);
			 return $row['cid'];
			}
		catch(PDOException $e){}
	}
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
	function genderedSingles($g)
	{
		$sql = "SELECT DISTINCT cid FROM citizens WHERE gender = '{$g}' AND relstat = 1 ORDER BY RAND()";
		$que = $this->db->prepare($sql);
		try { 
				$que->execute();
				$array = [];
				while($row = $que->fetch(PDO::FETCH_ASSOC))
				{
					$array[] = $row['cid'];
				}
		}catch(PDOException $e) { echo $e->getMessage();}
		return $array;
	}
	function updateSpouse($cit1,$cit2)
	{
			$sql = "UPDATE citizens SET spouse_id = '{$cit1}', relstat = '2' WHERE cid = '{$cit2}'; ";
			try { $this->db->exec($sql);}catch(PDOException $e) { echo $e->getMessage();}
	}
	function divorce($cid)
	{
		//* Right now this is used for Dead People
		$sql = "UPDATE citizens SET relstat = 1 WHERE (cid = {$cid} AND status = 1) OR spouse_id = {$cid}";
		$que = $this->db->prepare($sql);
		try { $que->execute();} catch(PDOException $e) { die($e->getMessage());}
	}
	function marryCitizens()
	{
		$men = $this->genderedSingles('m');
		$woman = $this->genderedSingles('f');
		#echo count($men)."|".count($woman)."\n";
			foreach($woman as $x=>$v)
			{
				if(count($woman) >= 0 or count($men) >= 0)
				{
					$cit1 = $v;
					if(mt_rand(0,10) <= 2)
					{
						//* This is the Gay Marriage Stuff
						if(count($men)-1 >= 0)
						{
							$cit1 = $men[mt_rand(0,(count($men)-1))];
							$cit2 = $men[mt_rand(0,(count($men)-1))];
							if($cit1 <> $cit2)
							{
								$po1 =  $this->getCitizenTile($cit1);
								$po2 = 	$this-> getCitizenTile($cit2);
								if($po1 == $po2)
								{
									$cit1age = $this->citizenAge($cit1);
									$cit2age = $this->citizenAge($cit2);
									if(($cit1age >= 18) && ($cit2age >= 18))
									{
										$this->updateSpouse($cit1, $cit2);
										$this->updateSpouse($cit2,$cit1);
											$name1 = $this->getname($cit1);
											$name2 = $this->getname($cit2);
											$men = $this->genderedSingles('m');
											$woman = $this->genderedSingles('f');
											$text = "{$name1['first_name']} {$name1['last_name']} AND {$name2['first_name']} {$name2['last_name']} married on {$this->getTime()}!";
										$this->message($text,'happy',2);
											/* Future Proofing this for later "Gay Marriage" */

									}
								}
							}
	
						}
					}
					else
					{
						
					if(count($men)-1 >= 0)
					{
					$cit2 = $men[mt_rand(0,(count($men)-1))];
					$cit1age = $this->citizenAge($cit1);
					$cit2age = $this->citizenAge($cit2);
						if(($cit1age >= 18) && ($cit2age >= 18))
						{
							$this->updateSpouse($cit1, $cit2);
							$this->updateSpouse($cit2,$cit1);
								$name1 = $this->getname($cit1);
								$name2 = $this->getname($cit2);
								$men = $this->genderedSingles('m');
								$woman = $this->genderedSingles('f');
								$text = "{$name1['first_name']} {$name1['last_name']} AND {$name2['first_name']} {$name2['last_name']} married on {$this->getTime()}!";
							$this->message($text,'happy',2);
								/* Future Proofing this for later "Gay Marriage" */

						}
					}
					else
					{
						if((count($woman)-1) >= 0)
						{
							$cit2 = $woman[mt_rand(0,(count($woman)-1))];
							$cit1age = $this->citizenAge($cit1);
							$cit2age = $this->citizenAge($cit2);
								if(($cit1age >= 18) && ($cit2age >= 18))
								{
									$this->updateSpouse($cit1, $cit2);
									$this->updateSpouse($cit2,$cit1);
										$name1 = $this->getname($cit1);
										$name2 = $this->getname($cit2);
										$men = $this->genderedSingles('m');
										$woman = $this->genderedSingles('f');
										$text = "{$name1['first_name']} {$name1['last_name']} AND {$name2['first_name']} {$name2['last_name']} married on {$this->getTime()}!";
									$this->message($text,'happy',2);
										/* Future Proofing this for later "Gay Marriage" */

								}
						}
						return true;
					}
				}
				}
				else
				{	
					return true;
				}
			
			}
		return true;
	}
}