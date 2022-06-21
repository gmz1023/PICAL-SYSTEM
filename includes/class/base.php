<?php
/*
	Basic Functions
*/
class base extends maths
{
	function __construct($db, $loop)
	{
		$this->db = $db;	
		$this->loop = $loop;
		#$this->debugger();
		$this->population = $this->totalPopulation();
	}
	function LogData($msg,$color,$level)
	{
		$sql = "INSERT INTO console(text,color,level) VALUES (:msg,:color,:lvl)";
		$que = $this->db->prepare($sql);
		$que->bindParam(':msg',$msg);
		$que->bindParam(':color',$color);
		$que->bindParam(':lvl',$level);
		try { $que->execute();}catch(PDOException $e) { die($e->getMessage());}
	}
	function iterationInfo($it,$loop)
	{
		$sql = "INSERT INTO `stats` (`sid`, `time_step`, `it`, `cycle`, `pop`, `pregnant`, `infected`, `dead`, `topDeathCause`, `last_name`, `sucessors`, `plants`, `air`, `water`, `wildlife`, `avgTemp`) VALUES (NULL, '', '{$it}', '{$loop}', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0');";
		try { $this->db->exec($sql);}catch(PDOException $e) { die($e->getMessage());}
	}
/* Messaging Functions
	General messages pass through message
	Death Messages pass through Death Message 
*/
	function lifeMessages($cid, $r =NULL)
	{
		/* These ones are actually people who have died... Book of the dead, book of the living */
		$name = $this->prettyName($cid);
		$age = $this->citizenAge($cid);
		if(extra_info >= 3)
		{
			if($r == NULL)
			{
			$file = 'xml.xml';
			$xml = simplexml_load_file($file);
			
			$path = $xml->xpath('deathnotes');
			
			#$random = array_rand($path);
			#$text = '';
			$message = [];
			foreach($path as $v)
			{
				foreach($v as $k)
				{
					$message[] = (string)$k;
				}
			}
			$c = count($message)-1;
			$text = $message[mt_rand(0,$c)];
			if(preg_match("/{name}/", $text))
			{
				$text = preg_replace("/{name}/", $name, $text);
				$text = preg_replace("/{age}/", $age, $text);
			}
			}
			else
			{
				$text = $name." Died of {$r} at Age {$age}";
			}
			$pop = $this->totalPopulation();
			#print_r(debug_backtrace());
			$msg = "[DEAD][{$cid}]".$text."| Pop Remaining: {$pop}";
			 $this->message($msg,'dead', 0);
			#	sleep(1);
			
		}
	}
	function message($message,$col, $level)
	{
		$message = strtoupper($message);
		if(extra_info >= $level)
		{
		switch($col)
		{
			case "info":
				$color = "blue";
				break;
			case "red":
				$color ="red";
				break;
			case "red-bg":
				$color ="red";
				break;
			case "happy":
				$color = "hap";
				break;
			case "green":
				$color ="green";
				break;
			case "blue":
				$color ="blue";
				break;
			case "yellow":
				$color ="yellow";
				break;
			case "danger":
				$color = "danger ";
				break;
			case "m":
				$color = "m"; 
				break;
			case "f":
				$color = "f"; 
				break;
			case 'inv':
				$color = "inv";
				break;
			default:
				$color ="normal";
				break;
		}
		$msg = "{$color} {$message} \e[0m \n";
		if(debug == 'on') {	echo $msg; }
			$this->LogData($message,$col,$level);
		}
	}
/**************************************************************
    
	 Future Section for DeBugging Information 
	 
*************************************************************/
	function debugger()
	{
		$sql = "SELECT * FROM citizens";
		$que = $this->db->prepare($sql);
		try { $que->execute();
				while($row = $que->fetch(PDO::FETCH_ASSOC))
				{
					print_r($row);
				}
		}catch(PDOException $e) { }
	}
/* End DeBug Section */
/**********************************************************


		Time Functions
		T

***********************************************************/
	function getTime()
	{
		/* Retrieves the Current Time from the Database */
		$sql = "SELECT simtime FROM timestep LIMIT 1";
		$que = $this->db->prepare($sql);
		try { 
			$que->execute(); 
			$row = $que->fetch(PDO::FETCH_ASSOC);
			$time =  $row['simtime'];
			$dtime = new datetime($time);
			$time = $dtime->format('d-m-Y H:i:s');
			return $time;
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			}	
	}
	function updateTime()
	{
		/* Updates the Time; TIME_STEP is defined in constants.php via TIME_CHOICE and TIME_STEP */
		$time = $this->getTime();
		$timeObj = new datetime($time);
		$year = $timeObj->format('Y');
		if($year >= 9999)
		{
			$year = 0001;	
		}
		else
		{
			$timeObj->modify(TIME_STEP);
			$time = $timeObj->format('Y-m-d H:i:s');
			$sql = "UPDATE timestep SET simtime = :time";
			$que = $this->db->prepare($sql);
			$que->bindParam(':time', $time);
			try { if($que->execute())
			{
				return true;
			}}catch(PDOException $e){ echo $e->getMessage(); exit;}
		}
	}
}
?>