<?php
class health extends biofunctions
{
	function run_the_guanlet()
	{
		$sql = "SELECT cid, health,thirst,hunger,inti FROM citizens WHERE status = 1 ORDER BY rand()";
		$que = $this->db->prepare($sql);
		$sup = $this->allSuplies();
		$ox = $sup['air'];
		try { 
			
			$que->execute(); 
			while($row = $que->fetch(PDO::FETCH_ASSOC))
			{
				$this->playerMove($row['cid']);
				$this->killCitizens($row['cid'],$row['health'],$row['thirst'],$row['hunger'],$ox,$row['inti']);
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
	function auto_healing($cid)
	{
		$th = $this->isThirsty($cid);
		$hu = $this->isHungry($cid);
		if($th && $hu)
		{
			$this->healthHitSilent($cid,10);
		}
	}
	function healthHitSilent($cid,$val)
	{
		$sql = "UPDATE citizens SET health = health+{$val} WHERE cid = {$cid}";
		try {
			$this->db->beginTransaction();
			$this->db->exec($sql);
			$this->db->commit();
		}catch(PDOException $e) { $this->db->rollback(); die($e->getMessage());}
	}
	function healthHit($cid, $val, $r)
	{
		$name = $this->prettyName($cid);
		$text = "[Health]".$name." Lost No Health -- Because this function is borked";
		$this->message($text,'green',10);
	}
	function getHealth($cid)
	{
		$sql = "SELECT health FROM citizens WHERE cid = {$cid} AND status = 1;";
		$que = $this->db->prepare($sql);

		try { 
			$que->execute();
			$row = $que->fetch(PDO::FETCH_ASSOC);
			return $row['health'];
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
/* Generic Disease Functions */
	function cancerHiter($cid)
	{
		$g = $this->getCitizenGenetics($cid);
		return ($g['BRAC1'] +$g['BRAC2'] + $g['BRAC3']);
	}
	function diseaseStuff($cid, $age)
	{
		$chance = 10;
		if($age >= 45)
		{
			#$this->healthHit($cid,mt_rand(4,$age),'age');
		}
		$g = $this->getCitizenGenetics($cid);
		$cancer = ($g['BRAC1'] +$g['BRAC2'] + $g['BRAC3']);
		$this->cancer($cid,$age,$cancer);
	}
	function immunityCheck($cid)
	{
		$g = $this->getCitizenGenetics($cid);
		$immunity = 0;
		foreach($g as $k=>$v)
		{
			if(preg_match("/imm[0-1][0-3]/", $k))
			{
				$immunity = $immunity+$v;
			}
			else
			{
				
			}
		}
		return $immunity;
		
	}
	/*
	Specific Genetic / Enviromental Functions -- Cancer
	*/
	function cancer($cid, $age, $c)
	{	
		if($c <= 0)
		{
			if($age <= mt_rand(45,100))
			{
				if(mt_rand(0,100) <= mt_rand(0,$age))
				{
					$val = mt_rand(-1,1);
					$gene = "BRAC".mt_rand(1,3);
					$this->mutation($gene, $val, $cid);
				}
				
			}
		}
		else
		{
			if(mt_rand(3,mt_rand(4,60)) == mt_rand(0,$c))
			{
				$hit = 15*$c;
				#$this->healthHit($cid,mt_rand(1,$c));
				$pretty = $this->prettyName($cid);
				$health = $this->getHealth($cid);
				$this->message("{$pretty} Withered from Cancer | $health",'red');
			}
		}
	}
	/* Killing of Citizens*/

}