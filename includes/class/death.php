<?php
class death extends supplies
{
	function kill($cid, $r)
	{
		$sql = "UPDATE citizens SET cod = '{$r}', died_on = (SELECT simTime FROM timestep LIMIT 1) WHERE cid = {$cid}; INSERT INTO gravestones SELECT * FROM citizens WHERE cid = {$cid};";
		$sql2 = "DELETE FROM citizens WHERE cid = {$cid};";
		$sql3 = "INSERT INTO dead_dna SELECT * FROM genetics WHERE cid = {$cid};";
		$sql4 = "DELETE FROM genetics WHERE cid = {$cid};";
		$time = $this->getTime();
			try {
				$this->divorce($cid);
				$this->lifeMessages($cid, $r);
				$this->db->exec($sql);
				$this->db->exec($sql3);

				$this->db->exec($sql2);
				$this->db->exec($sql4);
			}
		catch(PDOException $e) { 
			die($e->getMessage());
		}
		$this->deathvorce($cid);
	}
	function deathvorce($cid)
	{
		$sql = "UPDATE citizens SET relstat = 0 WHERE spouse_id = {$cid}";
		$que = $this->db->prepare($sql);
		try { $que->execute();}catch(PDOException $e) { die($e->getMessage());}
	}
	function lastDrankOn($cid)
	{
		$sql = "SELECT drankOn FROM citizens WHERE cid = {$cid}";
		$que = $this->db->prepare($sql);
		try  {
			$que->execute();
			$row = $que->fetch(PDO::FETCH_ASSOC);
			return $row['drankOn'];
		}catch(PDOException $e){}
	}
	function killCitizens($cid,$health)
	{
		$tid = $this->getCitizenTile($cid);
		$temp = $this->getTileStats($tid,'temp')['temp'];
		$gen1 = $this->getParentsGenetics($cid,0)[0];
		$gen = substr($gen1,30,5);
		$age = $this->citizenAge($cid);
		//echo "The Temp on {$tid} IS {$temp} \n";
		//* Rewriting this function to solve health issue.
		if($health > 0)
		{
			$array = array(
					'CCCC'=>array('val'=>5,'name'=>'Hyper-Disformia'),
					'AAAA'=>array('val'=>5,'name'=>'Hyper-Disformia'),
					'GGGG'=>array('val'=>5,'name'=>'Hyper-Disformia'),
					'TTTT'=>array('val'=>5,'name'=>'Hyper-Disformia'),
					'TATA'=>array('val'=>5,'name'=>'Hyper-Disformia'),
					'ACAT'=>array('val'=>5,'name'=>'Hyper-Disformia'),
					'GABA'=>array('val'=>5,'name'=>'Hyper-Disformia'));
			if(in_array($gen,$array))
			{
				$ar = $array[$gen];
				echo "{$cid} has {$ar['name']}";
				$this->healthHit($cid, '4', 'Genetic Disformia');	
			}
			$c = strlen($gen1);


			if($c < GENE_MAX)
			{
				//* Not enough genes, needs to be retooled)
				//echo $c."\n";
				$val = 101-$c;
				//$this->healthHit($cid,$val,'Genetic Misformia');
			}
			if($c > GENE_MAX)
			{
				//* This one works as it should but should be retooled
				$val = $c - 101;
				$this->healthHit($cid,$val,'Genetic Hyperformia');
			}
			if($age > 122)
			{
				$this->kill($cid, "old age");	
			}

		}
		else
		{
			$this->kill($cid, "Couldn't beat the reaper");
		}
	}
}