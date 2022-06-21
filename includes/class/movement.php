<?php
class movement extends bacteria
{
	/* 
		Movement Functions
		Rewritten on 13/6/22
	
	*/
	function playerMove($cid)
	{
		
		$limits = $this->getMapMax();

		$sid = $this->getCitizenTile($cid); // Probably can change this at some point too
		//Get Tile Information
		$data = $this->getTileStats($sid,'water,temp,max_pop,(wildlife+plants) as food');
		$food = $data['food'];
		$water = $data['water'];
		$max_pop = $data['max_pop'];
		$pop = $this->getTilePop($sid);
		if($food < food_consumption || $water < water_consumption)
		{
			$r = 'resources';
			$move = mt_rand($limits['min_lim'],$limits['max_lim']);
		}
		elseif($max_pop <= $pop)
		{
			$r = 'over population';
			$move = mt_rand($limits['min_lim'],$limits['max_lim']);
		}
		else
		{
			$r = '';
			$move = $sid;
		}
		
		if($move == $sid)
		{
			// Improvements
			$this->improvements($sid);
		}
		else
		{
		$this->updatePos($cid,$move,$move,$r);
		}
		$this->satisify_needs($cid,$sid);
	}
	function updatePOS($cid,$pos,$move,$r)
	{
				$name = $this->prettyName($cid);
		$sql = "UPDATE
					citizens
				SET
					tile_id = {$pos}
				WHERE
					cid = {$cid}
					OR
					spouse_id = {$cid}
					OR
					mother_id = {$cid}
					OR
					father_id = {$cid};";
		try { $this->db->exec($sql);}catch(PDOException $e) { die($e->getMessage());}
		$text = "[MOVEMENT] {$name} AND THEIR FAMILY MOVED TO {$move}";
		$this->message($text,'blue',3);
	}
}