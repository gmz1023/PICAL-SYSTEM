<?php
class health extends biofunctions
{
	function run_the_guanlet()
	{
		$sql = "SELECT cid, health,thirst,hunger,inti FROM citizens WHERE status = 1 ORDER BY rand()";
		$que = $this->db->prepare($sql);
//		$sup = $this->allSuplies();
		$ox = 100;
		try { 
			
			$que->execute(); 
			while($row = $que->fetch(PDO::FETCH_ASSOC))
			{
				$this->playerMove($row['cid']);
				$this->killCitizens($row['cid'],$row['health']);
				/* Uncomment the code below only when testing things that need a fresh run to die quickly */
				#$this->kill($row['cid'], 0);

						/* It should be noted that the "Infection Roll" which is called through Kill Citizens is being called twice (or more) from this calling of KillCitizens. It's probably an easy fix but it's not causing any issues right now so i'm ignoring it. */
						#if(mt_rand(0,9000) == 1)
						#{
						#	$rn = sprintf('%02d', mt_rand(0,12));
						#	$gene = "imm".$rn;
						#	#$this->mutation($gene, mt_rand(-2,5), $row['cid']);
						#}
			}
			#if(mt_rand(0,400))
			#{
			#			$this->virusMutation();
			#}
		}catch(PDOException $e) { echo $e->getMessage();}
	}
/* Actual Health Functions */
	function is_infected($cid)
	{
		$sql = "SELECT infected FROM citizens WHERE cid = {$cid} AND status = 1;";
		$que = $this->db->prepare($sql);
		try { $que->execute(); 
				$row = $que->fetch(PDO::FETCH_ASSOC);
			 	return $row['infected'];
			}catch(PDOException $e) { die("Is_Infected Failure! ".$e->getMessage());}
	}
	function healthHit($cid, $val, $r)
	{
		$name = $this->prettyName($cid);
		$h = $this->getHealth($cid);
		$val = abs($val);
		if($h-$val <= 0)
		{
			$this->kill($cid, $r);
		}
		else{
		$sql = "UPDATE citizens SET health = health-{$val} WHERE cid = {$cid}";
		try {
			$this->db->beginTransaction();
			$this->db->exec($sql);
			$this->db->commit();
		}catch(PDOException $e) { $this->db->rollback(); die($e->getMessage());}
		$text = "[Health]".$name." Lost {$val} Health -- {$r}";
		$this->message($text,'red',10);
		}
	}
	function healthRestore($cid,$v)
	{
		return true;
		
	}
	function getHealth($cid)
	{
		$sql = "SELECT health FROM citizens WHERE cid = {$cid} AND status = 1;";
		$que = $this->db->prepare($sql);
		//echo $sql."\n";
		try { 
			$que->execute();
			$row = $que->fetch(PDO::FETCH_ASSOC);
			if($row){
			return $row['health'];
			}
			else
			{

			}
		}catch(PDOException $e) { die("Get Health fault: ".$e->getMessage());}
		return true;
	}
	/* INfect Citizens with Virus */
	function infectionCheck($cid)
	{
		$vid = $this->is_infected($cid);
		
		if($vid > 0)
		{

			$tid = $this->getCitizenTile($cid);
			$list = $this->distanceCheck($tid, $cid);
			$count = count($list)-1;
			$this->infectRoll($vid, $list);
			#$this->healthHit($cid,mt_rand(0,10),'infection');

		}
		else
		{
			$pop = $this->population;
			$pop = $pop*4;
			$chance = mt_rand(0,$pop);
			if($chance == 4)
			{
				
				$vid = $this->selectRandomVirus();
				
				$this->infect($cid, $vid);
			}
		}
		
	}

}