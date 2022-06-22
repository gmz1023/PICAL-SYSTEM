<?php
class genes extends virus
{
	function getParentsGenetics($m,$d)
	{
		if($m == 0 || $d == 0)
		{
			$m = $this->newGenome();
			$d = $this->newGenome();
			$array = array($m,$d);
		}
		else
		{
		$sql = "SELECT genetics.genome as ag, dead_dna.genome as dg FROM genetics, dead_dna WHERE (genetics.cid = {$m} OR  genetics.cid = {$d}) OR (dead_dna.cid = {$m} OR dead_dna.cid = {$d})";
		$que = $this->db->prepare($sql);
		try { 
			$array = [];
			$que->execute();
			while($row = $que->fetch(PDO::FETCH_ASSOC))
			{
				$array[] =  $row['ag'] ? $row['ag'] : $row['dg']; 	
			}
		}
			catch(PDOEXception $e) { die($e->getMessage());}
		}
		return $array;
	}
	function newGenome()
	{
		$genome = '';
		$array = array('A','C','G','T');
		for($i = 0; $i < 100; $i++)
		{
			shuffle($array);
			$genome .= $array[0];
		}
		return $genome;
	}
	function geneticMixer($p, $birth = null)
	{
			$m = $p[0];
			$d = $p[1];
			$ml = strlen($m);
			$dl = strlen($d);
			$mito = substr($m,0,25);
			$seq = $mito;
			$dad = substr($d,25,$dl);
			$mom = substr($m,25,$ml);
			$s = $dad;
			$s .= $mom;
			$s = str_shuffle($s);
			$combo = substr($s,0,75);
			$seq .= $combo;
			if($birth == true && mt_rand(1,mutation_chance/1000) == 1)
			{
			 $seq=	$this->mutate($seq);
			}
			return $seq;
	}
	
	function insertNewGenetics($g)
	{
		/* Handles the new genetic inserts as well as mutations, as mentioned in the above functions, this should be chaned/simplified */
		$sql = "
		INSERT INTO genetics
		(genome)
		VALUES
		('{$g}')";
		
				
		#print_r($sql);
		#exit;

		
		try { $this->db->exec($sql);}catch(PDOException $e) { 
			print_r($sql);
			echo "\n";
			die('Genetic Fault: Sequence 01:'.$e->getMessage());}
	}
/*

	Update Genetics / Mutate Genetics

*/
	function ConstantMutationRate()
	{
		$sql = "SELECT 
			c.cid,
			g.genome as seq
		FROM
			citizens as c,
			genetics as g
		WHERE
			c.cid = g.cid
		AND
			status > 0
			";

	}
	function updateMutation($seq,$cid)
	{
		echo "Mutation has occured IN {$cid}) /n";
		$seq = $this->mutate($seq);
		$sql = "UPDATE genetics SET genome = :seq WHERE cid = :cid";
		$que = $this->db->prepare($sql);
		$que->bindParam(":cid",$cid);
		$que->bindParam(":seq",$seq);
		try { $que->execute();}catch(PDOException $e) { die($e->getMessage());}
	}
	function mutate($seq)
	{

		$mito = substr($seq,0,25);
		$array = array('del','insert','dup','inv');
		$c = count($array)-1;
		$s = $array[mt_rand(0,$c)];
		switch($s)
		{
			case 'del':
				$seq = str_split($seq);
				$c = count($seq)-1;
				unset($seq[mt_rand(0,$c)]);
				$seq = implode('',$seq);
				break;
			case 'insert':
				$array = array('A','C','G','T');
				$c = count($array)-1;
				$add = $array[mt_rand(0,$c)];
				$seq .= $add;
			break;
			case 'dup':
				$seq2 = str_split($seq);
				$c = count($seq2)-36;
				$x = 20+mt_rand(0,$c);
				$text = '';
				for($i = 1; $i <= 10; $i++)
				{
					$y = $x+$i;
					$seq .= $seq2[$y];
				}
			break;
			default:
				// Do nothing currently
				break;
				
		}
		return $seq;
	}

}