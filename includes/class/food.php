<?php 
class food extends genes
{
/* 
	Food / Need related Functions 
*/
	function updateSupplies()
	{
		if($this->loop % 1 == 0)
		{
		$food = mt_rand(1,10)*(($this->population)*10);
		$water = mt_rand(100,1000)*(1.416808);
		$med = mt_rand(10,900)*(1.516931);
		$sql = "UPDATE supplies SET food = food+({$food}), water = water+($water), medicine=medicine+($med) WHERE (water < 1000) AND (food < 1000) AND (medicine < 1000)";
		//* When Oxygen is working uncomment this
		# $sql .= " air = air+{$val}";
		//* When Medicine is working uncomment this
		# $sql .= " medicine = medicine+{$val}";
		try { 
			$this->db->beginTransaction();
			$this->db->exec($sql);}catch(PDOException $e) { die($e->getMessage());}
			if($this->db->commit())
			{
				$sup = $this->allSuplies();
				$this->message("[SUPPLY DROP] {$food}/{$sup['food']} FOOD {$water}/{$sup['water']}  WATER {$med}/{$sup['medicine']}  MEDICINE", 'green', 0);
			}
		}
		else { //* No Supply Drop

		}
	}
	function allSuplies()
	{
		$sql = "SELECT * FROM supplies";
		$que = $this->db->prepare($sql);
		try { $que->execute();
			$row = $que->fetch(PDO::FETCH_ASSOC);	
			 return $row;
			}catch(PDOException $e) { echo $e->getMessage();}
	}
	function hunger($cid)
	{
		$max = 26417;
		$val= mt_rand (1, $max) / 100000;
		#echo $val."\n";
		$sql = "UPDATE citizens SET hunger = hunger-($val), thirst=thirst-($val), air = air-(10) WHERE cid = {$cid}";
		try {
				$this->db->beginTransaction();
				$this->db->exec($sql);
				$this->db->commit();
			return true;
			#echo $sql;
			#exit;
		}catch(PDOException $e) { echo die("Hunger Fault: ".$e->getMessage());}
	}
	function supplyCheck($name)
	{
		$sql = "SELECT {$name} FROM supplies";
		$que = $this->db->prepare($sql);
		try { $que->execute();
			$row = $que->fetch(PDO::FETCH_ASSOC);	
			 return $row[$name];
			}catch(PDOException $e) { echo $e->getMessage();}
	}
	function eat($cid,$rName)
	{
		#$value = abs($val)*10;
		$name = $this->prettyName($cid);
		$need = $rName;
		switch($rName)
		{
			case 'thirst':
			$resource = 'water';
			$value = mt_rand(0,0.26417205);
			break;
			case 'hunger':
			$resource = 'food';
			$value = mt_rand(0.0001,0.1);
			break;
			case 'health':
			$resource = 'medicine';
			$value = mt_rand(0.5,1.750);
			break;	
			case "air":
			$resource = "air";
			$value = mt_rand(5,20);
			break;
			default:
			$resource = '';
			break;
			

		}
		if($this->supplyCheck($resource)-$value > 0)
		{
			if($resource == 'medicine' && mt_rand(0,5) == 5)
			{
				$this->infect($cid, '0');
			}
		$sql = "UPDATE 
				citizens, 
				supplies 
				SET 
					citizens.{$need} = citizens.{$need}+{$value}, 
					supplies.{$resource} = supplies.{$resource}-({$value}) 
					WHERE supplies.{$resource} >= 0 AND relStat > 0 AND (citizens.{$need}+{$value} <= 100) AND cid = {$cid};";
		try { 
			$this->db->beginTransaction();
			$this->db->exec($sql);
			if($this->db->commit())
			{
				$text = "[RESOURCE] ".$name." Consumed {$resource}";
				$this->healthHit($cid,mt_rand(-5,-1),$rName);
				$this->message($text, 'yellow', 2);
			}
			else
			{
				$text = "[RESOURCE] ".$name." went a day without {$resource}";
					$this->message($text, 'red', 2);
			}
		}catch(PDOException $e) { echo $e->getMessage(); } 
		}
		else
		{
					$text = "[RESOURCE] ".$name." went a day without {$resource}";
					$this->message($text, 'danger', 2);
		}
	}
	
}