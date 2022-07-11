<?php
require_once('./includes/db.php');
$_SESSION = array('rc'=>0,'it'=>0,'loop'=>0);

$starttime = '1000-00-00 00:00:00';
$i = 0;
$x = 0;
do{
	$cit = new base($db,$_SESSION['loop']);
	$start = microtime(true); // Start the clock
	$cit->rain(); // Enviromental Systems -- may need to be spinned off eventually
	$cit->do_run(); // Force the citizens through the gauntlet
	//sleep(1);
	$pop = $cit->totalPopulation(); // Population Counter
	$cit->ConstantMutationRate(); // Mutate the Citizens (WIP)
	$end = microtime(true); // End the Clock
	$r = $cit->getGenderRatio();
	$time = $cit->getTime();
	$iq = $cit->averageIQ();
	$iq = is_null($iq) ? 0 : ceil($iq);
	
	$_SESSION['it']++;
	/* Information About Iteration */
	$msg =  'Iteration'.$_SESSION['it'].' Loop '.$_SESSION['loop']." Pop :".$pop. " IQ : {$iq} | {$r} | Execution Time {$time}\n";
	echo $msg;
	$cit->message($msg,'info',9);
	echo $end-$start."\n";
	$cit->bet();
	$cit = '';

}while(1 == 1);

//include('genetics.php');
//include('run.php');
?>