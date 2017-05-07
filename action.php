<?php
require_once('includes/db.php');
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
$cit = new base($db);
	$iq = $cit->averageIQ();
$population = $cit->totalPopulation();
$infect = $cit->selectInfectedPop();
$oPop = $cit->overallPopulation();
$starttime = '1000-00-00 00:00:00';
	$time = $cit->getTime();
	$d1 = new DateTime($time);
	$d2 = new DateTime($starttime);
	$diff = $d2->diff($d1);

if($population <= 1)
{
	$day = $diff->d;
	echo "Population 0  | infected {$infect} | {$cit->getTime()} | Sim Length: ".$diff->y." Years ".$diff->m." Months ".$diff->d." Days | Average IQ {$iq} \n";
	exit;
}
else
{
			$cit->do_run();
			echo "Time: {$cit->getTime()} | Population: {$population}/{$oPop} | infected {$infect} | A.p | Sim Length: ".$diff->y." Years ".$diff->m." Months ".$diff->d."Days | Average IQ {$iq} \n";
			include('action.php');
}
end;