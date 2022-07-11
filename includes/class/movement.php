<?php
class movement extends bacteria
{
	/* 
		Movement Functions
		Rewritten on 13/6/22
	
	*/
	function MoveMath($sid)
	{
		
		$u = ($sid-18) > 1 ? ($sid-18) : 360;
		$d = ($sid+18) < 360 ? ($sid+18) : 1;
		$l = ($sid+1) < 360 ? ($sid+1) : 360;
		$r = ($sid-1) > 1 ? ($sid-1) : 1;
		$ar = array($sid,$u,$d,$l,$r);
		shuffle($ar);
		return $ar[0];
		
	}
	function playerMove($cid)
	{
		
		$limits = $this->getMapMax();

		$sid = $this->getCitizenTile($cid); // Probably can change this at some point too
		//Get Tile Information
		$data = $this->getTileStats($sid,'water,temp,max_pop,(wildlife+plants) as food'); // Probably not a safe system, should update it at some point
		$food = $data['food'];
		$water = $data['water'];
		$max_pop = $data['max_pop'];
		$pop = $this->getTilePop($sid);
		$age = $this->citizenAge($cid);
		if($food < food_consumption || $water < water_consumption)
		{
			$r = 'resources';
			$move = $this->MoveMath($sid);
		}
		elseif($max_pop <= $pop)
		{
			$r = 'over population';
			$move = $this->MoveMath($sid);;
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
			$new_tile_stats = $this->getTileStats($move,'type');
			$type = $new_tile_stats['type'];
			if($type == '2')
			{
				// Water Tile Cannot Move Too
				$move = $sid;
			}
			else
			{
				// Do Nothing
			}
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