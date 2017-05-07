<?php
class genes extends virus
{
	function getCitizenGenetics($cid)
	{
				$sql = "SELECT
					infert1,
					BRAC1,
					BRAC2,
					BRAC3,
					stnk1,
					imm00,
					imm01,
					imm02,
					imm03,
					imm04,
					imm05,
					imm06,
					imm07,
					imm08,
					imm09,
					imm10,
					imm11,
					imm12,
					g.gdisorder
				FROM genetics as g
				WHERE cid = {$cid}
				LIMIT 1;";
		$que = $this->db->prepare($sql);
		try { 
				$que->execute();
				$row = $que->fetch(PDO::FETCH_ASSOC);
			
			return $row;
			
		}
		catch(PDOException $e) { echo $e->getMessage();}
	}
	function getParentsGenetics($m,$d)
	{
		/* Select Genetic Information from a single parent: ideally this would be altered to mix the two 
		but I'm working to get the whole thing "Generally" functional, not "Fully" functional.
		Right now it returns the fetch array (Through newCitizens -- that should change too) to insertNewGenetics where the mutations are handled
		*/
		$sql = "SELECT
					infert1,
					BRAC1,
					BRAC2,
					BRAC3,
					stnk1,
					imm00,
					imm01,
					imm02,
					imm03,
					imm04,
					imm05,
					imm06,
					imm07,
					imm08,
					imm09,
					imm10,
					imm11,
					imm12,
					g.gdisorder
				FROM genetics as g
				WHERE cid in({$m},{$d})
				ORDER BY rand()
				LIMIT 1;";
		$que = $this->db->prepare($sql);
		try { 
				$que->execute();
				$row = $que->fetch(PDO::FETCH_ASSOC);
			
			return $row;
			
		}
		catch(PDOException $e) { echo $e->getMessage();}
	}
	function insertNewGenetics($g)
	{
		/* Handles the new genetic inserts as well as mutations, as mentioned in the above functions, this should be chaned/simplified */
		$keys = '';
		$val = '';
					#print_r($g);
		foreach($g as $x=>$v)
		{

			if(mt_rand(0,99999999) <= mt_rand(0,999))
			{
				$val .= $v+mt_rand(-1,1).","; 
			}
			else
			{
				$val .= $v.","; 
			}
			$keys .= $x.",";
		}
		$key_trim = trim($keys,',');
		$val_trim = trim($val,',');
		$sql = "
		INSERT INTO genetics
		(".$key_trim."
		)
		VALUES
		({$val_trim})";
		
				
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
	function mutation($gene, $val, $cid)
	{
		/* Can only change a gene plus or minus */
		$sql = "UPDATE genetics SET {$gene} = {$gene}+({$val}) WHERE cid = {$cid};";
		try { 
			$this->db->beginTransaction();
			$this->db->exec($sql);
			$this->db->commit();
			$this->message('Mutation Occured!', 'blue', '3');
		}catch(PDOException $e) { die("Mutation Fault: ".$e->getMessage());}
	}
}