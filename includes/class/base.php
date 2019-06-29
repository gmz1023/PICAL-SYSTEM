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
/* Messaging Functions
	General messages pass through message
	Death Messages pass through Death Message 
*/
	function deathMessage($cid, $health)
	{
		/* not actually dead people messages... these are those who survive */
		$name = $this->prettyName($cid);
		if(extra_info >= 3)
		{
			$file = 'xml.xml';
			$xml = simplexml_load_file($file);
			
			$path = $xml->xpath('surnotes');
			
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
			}
			echo "\e[1;36m  [SURVIVOR]".$text."| \e[0m \n";
				usleep(msg_delay);
		}
	
	}
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
			echo "\e[1;41m  [DEAD][{$cid}]".$text."| Pop Remaining: {$pop} \e[0m \n";
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
				$color = "\e[7m ";
				break;
			case "red":
				$color ="\e[1;31m ";
				break;
			case "red-bg":
				$color ="\e[3;37m ";
				break;
			case "happy":
				$color = "\e[22;32m ";
				break;
			case "green":
				$color ="\e[1;32m ";
				break;
			case "blue":
				$color ="\e[1;34m ";
				break;
			case "yellow":
				$color ="\e[1;33m ";
				break;
			case "danger":
				$color = "\e[1;41m ";
				break;
			case "m":
				$color = "\e[34m "; 
				break;
			case "f":
				$color = "\e[35m "; 
				break;
			case 'inv':
				$color = "\e[7m ";
				break;
			default:
				$color ="\e[1;36m ";
				break;
		}
		echo "{$color} {$message} \e[0m \n";
		}
		usleep(msg_delay);
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