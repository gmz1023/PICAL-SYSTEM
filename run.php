<?php
require_once('includes/db.php');

if(isset($_SESSION['it']))
{
	$_SESSION['it'] = $_SESSION['it']+1;
	$_SESSION['loop'] = 0;
}
else
{
	$_SESSION['it'] = 0;
	$_SESSION['loop'] = 0;
}
$it = $_SESSION['it'];
while(true)
{

	if(sleep_state == 'on') { 
	switch(sleep_type)
	{
		case 'u':
			usleep(sleep_var);
			break;
		case 's':
			sleep(sleep_var);
			break;
		default:
			sleep(sleep_var);
			break;
	}
} 
$cit = new base($db,$_SESSION['loop']);
	$iq = $cit->averageIQ();

$population = $cit->totalPopulation();
$infect = $cit->selectInfectedPop();
$oPop = $cit->overallPopulation();
$starttime = '1000-00-00 00:00:00';
	$time = $cit->getTime();
	$d1 = new DateTime($time);
	$d2 = new DateTime($starttime);
	$diff = $d2->diff($d1);
if($d1->format('m') == 12 && $d1->format('d') == 25 )
{
	$cit->message("MERRY MURDERVERSARY!",'green',9);
}

if($population == -1)
{
	$cit->stats();
	$_SESSION['loop'] = $_SESSION['loop']+1;
	$loop = $_SESSION['loop'];
	$day = $diff->d;
	echo "\e[7m INT {$it}.{$loop}|Pop 0/{$oPop}|infected {$infect}|{$cit->getTime()}|Sim Length: ".$diff->y." Years ".$diff->m." Months ".$diff->d." Days|Average IQ {$iq} | [Average Temp] {$cit->selectAverageTemperature()} \e[0m  \n";
	if($cit->stats())
	{
	sleep(1);
	#include('run.php');
	echo "\e[41;4;33m";
		echo "PICAL PROGRAM VERSION {$_SESSION['loop']} \e[0m \n";
		sleep(1);
		echo "\e[41;4;33m";
		echo "STATUS: FAILED \n";
		echo "\e[41;4;33m";
		echo "LOAD SEQ. \n \e[0m ";
		echo "";
		sleep(1);
		echo "\n";
		sleep(1);
		echo "\n";
		include('reset.php');
		include('run.php');
		exit;
	}
}
else
{
	if($_SESSION['loop'] % 2 == 0)
	{
		$cit->stats();
		$cit->message('[STATS RECORDED]','green',0);
	}
			$cit->do_run();
		$_SESSION['loop'] = $_SESSION['loop']+1;
	$loop = $_SESSION['loop'];

			echo "\e[7m INT {$it}.{$loop} | Time: {$cit->getTime()} | Pop:{$population}/{$oPop} | infected {$infect}| TFS: ".$diff->y." Years ".$diff->m." Months ".$diff->d."Days | AverageIQ {$iq}| | [Average Temp] {$cit->selectAverageTemperature()}\e[0m \n";
}
}
?>