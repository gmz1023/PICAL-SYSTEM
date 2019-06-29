<?php
class health extends supplies
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
						if(mt_rand(0,9000) == 1)
						{
							$rn = sprintf('%02d', mt_rand(0,12));
							$gene = "imm".$rn;
							$this->mutation($gene, mt_rand(-2,5), $row['cid']);
						}
			}
			if(mt_rand(0,400))
			{
						$this->virusMutation();
			}
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
		$text = "[Health]".$name." Lost No Health -- Because this function is borked";
		$this->message($text,'green',10);
	}
	function healthHitOld($cid, $val, $r)
	{
		$val = $val*1.2;
		#$h = $this->getHealth($cid);
		$sql = "UPDATE citizens SET health = health-({$val}) WHERE cid = {$cid} AND (health-{$val} <= 100 ) AND status = 1;";
		try { 	$this->db->beginTransaction();
				$this->db->exec($sql);
			 	$this->db->commit();
					$name = $this->prettyName($cid);
			
			 if($val > 0)
			 {
				
				 $health = $this->getHealth($cid);
				 if(($health-$val) > 0)
				 {
					  $color = 'red';
				 $text = "[HEALTH]".$name." LOST ".abs($val)."of HEALTH | {$r}";
					 			 $this->message($text,$color,10);
			 	 }
				 else
				 {
					$color = '';
					$text = $r;
					 
					#$this->kill($cid,$text, $r);
				 }
			 }
			 else
			 {
				 $color = 'green';
				 $text = "[HEALTH]".$name." GAINED ".abs($val)." HEALTH | {$r}";
				 			 $this->message($text,$color,10);
			 }
			# $text = "[HEALTH]".$name." LOST {$val} HEALTH";
			}catch(PDOException $e) { 
				$this->db->rollBack(); 
				die("Health Fault: ".$e->getMessage());}
		
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
	function kill($cid, $r)
	{
		$sql = "UPDATE citizens SET status = -1, died_on = :time, cod = :r WHERE cid = :cid AND status = 1";
		$que = $this->db->prepare($sql);
		$time = $this->getTime();
		$que->bindParam(':cid', $cid);
		$que->bindParam(':time', $time);
		$que->bindParam(':r', $r);
			try { 
				if($que->execute())
				{	
					$this->divorce($cid);
					$this->lifeMessages($cid, $r);	
				}
			}
		catch(PDOException $e) { 
			die($e->getMessage());
		}
	}
	function killCitizens($cid, $health, $thirst, $hunger)
	{
		//* Rewriting this function to solve health issue.
		$age = $this->citizenAge($cid);
		$name = $this->prettyName($cid);
		$cod = mt_rand(0, 900);
		if($health > 0)
		{
			//* This Function needs to be fully overhauled to properly support everything
		}
		else
		{
			$this->kill($cid,'Because I Said So');
		}
	}
	function killCitizensOld($cid, $health, $thirst, $hunger, $air)
	{
		/* This doesn't actually kill anyone, this hsould be change to Immunnity Roll */

		/* Run the Guantlet of killing them, check everything */
		$age = $this->citizenAge($cid);
		#$chance_of_death = (abs($thirst+$hunger-$air));
		$chance_of_death = mt_rand(0,900);
		$cancer = 4;
		$immunity = $this->immunityCheck($cid);
		$this->hunger($cid);	
	
		/* Infection Of Citizens */
		$this->infectionCheck($cid);
		if($health > 0)
		{
			$hit = mt_rand(0,$age) / 10;
			$this->diseaseStuff($cid,$age);
			#$this->healthHit($cid,$hit,'health');
			if($health < 50)
			{
				$x = ceil(mt_rand(0,$this->population*15)/3.8819);
				#$this->eat($cid,'health',$x);
			}
			if($hunger < 99)
			{
				$x = ceil(mt_rand(0,$this->population*25)/3.8819);
				$this->eat($cid,'hunger');
				
				#$this->healthHit($cid,$hit,'hunger');
			}
			elseif($hunger <= 0)
			{
				$this->kill($cid,'hunger');
			}
			else
			{ 
				//do nothing 
			}
			if($thirst <= 45)
			{
				#$this->healthHit($cid,$hit,'thirst');
				$x = ceil(mt_rand(0,$this->population*8)/3.8819);
				$this->eat($cid,'thirst');
			}
			elseif($thirst <= 0 )
			{
				$this->kill($cid,'thirst');
			}
			else
			{ 
				//do nothing 
			}
			if($air < 90)
			{
				$this->eat($cid,'air');
			}
			elseif($air <= 45 )
			{
				$this->healthHit($cid,$hit,'air');
			}
			elseif($air <= 0)
			{
				$this->kill($cid,'suffication');
			}
		}
		else
		{
			$this->kill($cid,'poor health');
		}
		return true;
	}
}