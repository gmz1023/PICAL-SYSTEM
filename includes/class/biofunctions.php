<?php
class biofunctions extends death
{
	function breathe()
	{
		$sql = "UPDATE 
				Atmosphere as a 
				SET 
				oxygen = oxygen+(SELECT sum(m.plants-(c.cid+m.wildlife)) FROM citizens as c, map as m WHERE status <> -1)/20068000,
				CoTwo = CoTwo+(SELECT sum(m.plants-(c.cid+m.wildlife)) FROM citizens as c, map as m WHERE status <> -1)/1400000000,
				methane = methane+(SELECT sum(cid) FROM citizens)/2800000000
							;";
		try{ $this->db->exec($sql);}catch(PDOexception $e){ die("Didn't Breath Right".$e->getMessage());}
	}

	function breathe2()
	{
		die();

	}
//* Probably should put this in it's own class at some point

}