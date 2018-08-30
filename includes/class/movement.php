<?php
class movement
{
	function playerMove($cid)
	{
		$limits = $this->getMapMax();
		$sid = $this->getCitizenTile($cid);
		$move = mt_rand(-1,1);
		$pos = $sid+$move;
		if($pos >= $limits['max_lim']+1)
		{
			$pos = 1;
		}
		if($pos <= 0)
		{
			$pos = $limits['max_lim']+1;
		}
		if($move != 0 ) {$this->updatePos($cid,$pos, $move);};
	}
	function updatePos($cid,$pos, $move)
	{
		$spouse = $this->getSpouse($cid);
		
		$sql = "UPDATE
					
					";
		try { $this->db->exec($sql);}catch(PDOException $e) {}
		$name = $this->prettyName($cid);
		#$text = "[MOVEMENT]{$name} MOVED {$move} SPACES TO {$pos}";
		#$this->message($text,'blue',30);
	}
	
}