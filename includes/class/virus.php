<?php
class virus extends enviroment
{
	
	function virusCount()
	{
		$sql = "SELECT count(vid) as vc FROM virus";
		$que = $this->db->prepare($sql);
		try { $que->execute(); $row = $que->fetch(PDO::FETCH_ASSOC);
			return $row['vc'];
			}catch(PDOException $e) { echo $e->getMessage(); }
	}
	function getVirusName($vid)
	{
		$sql = "SELECT name FROM virus WHERE vid = {$vid}";
		$que = $this->db->prepare($sql);
		try { 
			$que->execute(); 
			$row = $que->fetch(PDO::FETCH_ASSOC);
			return $row['name'];
		}catch(PDOException $e) { die($e->getMessage()); exit;}
	}
	function virusNameGen($length = 2)
	{
		$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
			$randomString .= mt_rand(0,9);
		}
		return $randomString;

	}
	function selectRandomVirus()
	{
		$sql = "SELECT vid FROM virus ORDER BY rand() LIMIT 1";
		$que = $this->db->prepare($sql);
		try { $que->execute(); $row = $que->fetch(PDO::FETCH_ASSOC);
			return $row['vid'];
			}catch(PDOException $e) { echo $e->getMessage(); }
	}
	function createVirus()
	{
		$name = $this->virusNameGen();
		$inf = mt_rand(-3,1);
		$sql = "INSERT INTO `lurch`.`virus` (`vid`,`name`, `infert1`, `type`, `imm00`, `imm01`, `imm02`, `imm03`, `imm04`, `imm05`, `imm06`, `imm07`, `imm08`, `imm09`, `imm10`, `imm11`, `imm12`, `death_rate`, `airb`, `waterb`, `contact`) 
			VALUES (NULL,'{$name}',{$inf},";
		$x = '';
		for($i = 1; $i <= 18; $i++)
		{
			$x .= mt_rand(0,1).",";
		}
		$trim = trim($x,',');
		$sql .= $trim.");";

		try { $this->db->exec($sql);}catch(PDOException $e) { die('New Virus Fault: '.$e->getMessage());}
	}
	function virusMutation()
	{
		
		$vid = $this->selectRandomVirus();
		$rn = sprintf('%02d', mt_rand(0,12));
		$gene = "imm".$rn;
		$val = mt_rand(-1,1);
		$sql = "UPDATE virus SET {$gene} = {$gene}+({$val}) WHERE vid = {$vid};";
		try { 
			$this->db->beginTransaction();
			$this->db->exec($sql);
			$this->db->commit();
			$vname = $this->getVirusName($vid);
			$this->message("[VIRUS]{$vname}-{$gene} mutated !", 'danger', '2');
		}catch(PDOException $e) { die("Viral Mutation Fault: ".$e->getMessage());}
	}
	function infectionCheck($vid)
	{
		$g = $this->getVirusGenes($vid);
		$immunity = 0;
		return $immunity;
		
	}
	function getVirusGenes($vid)
	{
		$sql = "SELECT
					*
				FROM
					virus
				WHERE
					vid = {$vid}
				";
		$que = $this->db->prepare($sql);
		
		try { 
			$que->execute();

				$row = $que->fetch(PDO::FETCH_ASSOC);
			 	return $row;
			} catch(PDOException $e){ echo $e->getMessage();}
	}
	/* The Battle */
	function infect($cid, $vid)
	{
		$sql = "UPDATE citizens SET infected = {$vid} WHERE cid = {$cid} AND infected <> 0";
		
		try {
			$this->db->beginTransaction();
			$this->db->exec($sql);
			if($this->db->commit())
			{
				$name = $this->prettyName($cid);
				$vname = $this->getVirusName($vid);
				if($vid > 0)
				{
					$this->message("[INFECTION]".$name." Became infected with ".$vname, 'danger', '2');
				}
				else
				{
					if(true)
					{
					$this->message("[INFECTION]{$name} no longer infected", 'blue', '2');
					}
				}
			}
			}catch(PDOException $e) { 
			$this->db->rollback();
			die("Infection Fault 01: ".$e->getMessage()); echo "\n";}
	}
	function infectRoll($vid, $cid)
	{
		$sql = "SELECT
					c.cid as cid,
					v.vid as vid,
					v.imm00-c.imm00  as v0,
					v.imm01-c.imm01  as v1,
					v.imm02-c.imm02  as v2,
					v.imm03-c.imm03  as v3,
					v.imm04-c.imm04  as v4,
					v.imm05-c.imm05  as v5,
					v.imm06-c.imm06  as v6,
					v.imm07-c.imm07  as v7,
					v.imm08-c.imm08  as v8,
					v.imm09-c.imm09  as v9,
					v.imm10-c.imm10  as v10,
					v.imm11-c.imm11  as v11,
					v.imm12-c.imm12  as v12,
					v.airb as a,
					v.waterb as w,
					v.contact as c
					FROM genetics as c, virus as v, citizens as cit
                    WHERE v.vid = {$vid} AND c.cid in(";
		$data = '';
		foreach($cid as $x=>$v)
		{
			$data .= $v.',';
		}
		print_r($data);
		$sql .= trim ($data,',');
		$sql .= ") AND cit.infected <> 0 AND cit.cid = c.cid ;";
		try { 
			 	$que = $this->db->prepare($sql);
			 	$que->execute();
			 	while($row = $que->fetch(PDO::FETCH_ASSOC))
				{
					if($row['vid'] <= 0)
					{
						//* Nothing
					}
					else
					{
						#print_r($row);
						if((array_sum($row)-($row['cid']+$row['vid'])) <=0)
						{
							$x = $row['a']+$row['w']+$row['c'];
							$chance = mt_rand(0,(100-$x));
							if($chance == 1)
							{
							$this->infect($row['cid'],$vid);
							}
						}
						else
						{
							$this->infect($row['cid'], 0);
						}
					}
				}
			
		
			}catch(PDOException $e) { die("Infection Roll Fault: ".$e->getMessage()); echo "\n"; exit;}	

	}
}