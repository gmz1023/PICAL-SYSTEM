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
	function deathMessage_old($cid, $health)
	{
		$name = $this->prettyName($cid);
		if(extra_info >= 3)
		{
		switch(mt_rand(1,16))
			{
				case 1:
				$text ="{$name} fought back the reaper, Not Today Death!";
				break;
				case 2:
				$text = "Despite crippling odds, {$name} is still alive!";
				break;
				case 3:
				$text = "{$name} keeps on keeping on!";
				break;
				case 4: 
					$text = "{$name} beat Death with their own cold hand";
				break;
				case 5:
					$text = "{$name} lives to see another day";
				break;
				case 6:
					$text = "{$name} Beat Death in a game of chess!";
					break;
				case 7: 
					$text = "{$name} narrowly avoided The Reaper!";
					break;
				case 8: 
					$text = "{$name} had a dance off with Death and won!";
					break;
				case 9: 
					$text = "{$name} had a dance off with Death and won!";
					break;
				case 10:
					$text = "What do yo umean {$name} survived? I had my bets on them dying!";
					break;
				case 11:
					$text = "After constructing a series of tubes and rodes, {$name} managed to extend there life for another day!";
					break;
				case 12:
					$text = "Long story short, that's how {$name} survived another day!";
					break;
				case 13:
					$text = "{$name} withered away while fighting the Ender Dragon";
					break;
				case 14:
					$text = "{$name} found the fountain of youth! Sadly only a days supply was left.";
					break;
				case 15:
					$text = "{$name} drank from their trusty Goddard Futuristics canteen";;
					break;
				case 16:
					$text = "{$name}'s final words 'Ducks, why did it have to be Ducks.'";
					break;
				default:
				$text = NULL;
				break;
			}
				if(!is_null($text))
				{
				echo "\e[1;36m  [SURVIVOR]".$text."\e[0m \n";
				usleep(msg_delay);
				}	
		}
	
	}
	function lifeMessages($cid, $r =NULL)
	{
		/* These ones are actually people who have died... Book of the dead, book of the living */
		$name = $this->prettyName($cid);
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
			}
			}
			else
			{
				$text = $name." Died of Hunger";
			}
			echo "\e[1;41m  [DEAD]".$text."| \e[0m \n";
				usleep(msg_delay);
			
		}
	}
	function message($message,$col, $level)
	{
		if(extra_info >= $level)
		{
		switch($col)
		{
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
				$color ="\e[44m ";
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
		echo "{$color} {$message}\e[0m \n";
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