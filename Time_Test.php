<?php
$date = "0:1:01:00:00:00";
define('TIME_STEP','40');

function changeTime($date,$step)
{
	$date = explode(':',$date);
	$date = array('sec'=>$date[5],'min'=>$date[4],'hour'=>$date[3],'day'=>$date[2],'month'=>$date[1],'year'=>$date[0]);
	$date[$step] = $date[$step]+TIME_STEP;
		if($date['sec'] >= 60)
		{
			$date['sec'] = 0;
			$date['min'] = $date['min']+1;
		}
		if($date['min'] >= 60)
		{
			$date['min'] = 0;
			$date['hour'] = $date['hour']+1;
		}
		if($date['hour'] >= 24)
		{
			$date['hour'] = 0;
			$date['day'] = $date['day']+1;
		}
		if($date['day'] >= 30)
		{
			$date['day'] = 1;
			$date['month'] = $date['month']+1;
		}
		if($date['month'] >= 12)
		{
			$date['month'] =0;
			$date['year'] = $date['year']+1;
		}
	
	
	return $date;
	
}
function displayTime($date)
{
	$date =array_reverse($date);
	$date =implode(':',$date);
	return $date;
}
for($i = 1; $i <> 0; $i++)
{
	$date = changeTime($date,'min');
	$date = displayTime($date);
	echo $date."\n";
}

