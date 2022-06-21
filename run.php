<?php
require_once('./includes/db.php');
$_SESSION = array('rc'=>0,'it'=>0,'loop'=>0);

$starttime = '1000-00-00 00:00:00';
$i = 0;
$x = 0;
do{
	$cit = new base($db,$_SESSION['loop']);
	$start = microtime(true);
	$cit->rain();
	$cit->do_run();
	//sleep(1);
	$pop = $cit->totalPopulation();
	$cit->ConstantMutationRate();
	$end = microtime(true);
	$r = $cit->getGenderRatio();
	$time = $cit->getTime();
	$iq = $cit->averageIQ();
	$_SESSION['it']++;
	echo 'Iteration'.$_SESSION['it'].' Loop'.$_SESSION['loop']." Pop".$pop. " IQ : {$iq} | {$r} | Execution Time {$time}\n";
	echo $end-$start."\n";
	$cit = '';

}while(1 == 1);

//include('genetics.php');
//include('run.php');
?>