<?php
class death extends supplies
{
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
}