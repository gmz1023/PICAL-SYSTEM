<?php
class users extends maths
{
	function bet()
	{
		$sql = "SELECT 
					c.cid,
					b.cid,
					b.wager,
					b.uid
				FROM 
					citizens as c,
					bets as b
				WHERE
					(c.status > 0
					AND
					b.cid = c.cid)
				";
		$que = $this->db->prepare($sql);
		try { 
				$que->execute();
				while($row = $que->fetch(PDO::FETCH_ASSOC))
				{
					if($row)
					{
					$this->payout($row['uid'],$row['wager']);
					//echo $row['uid'].' WAS PAID OUT '.$row['wager']."\n";
					}
					else
					{
					 //	echo "No Payment\n";
					}
				}
		}catch(PDOException $e) { die($e->getMessage()); }
	}
	function payout($uid,$wager)
	{
		$sql = "UPDATE 
					users
				SET
					points = points+{$wager}
				WHERE
					uid = {$uid};";
		try { $this->db->exec($sql);}catch(PDOException $e) { die($e->getMessage());}
	}
}