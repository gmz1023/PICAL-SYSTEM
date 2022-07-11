<?php

?>
<div id='container'>
<div class='header'>
	<div class='logo'><img src="style/img/shutterstock_304609643.jpg" width='50px' height='50px' /> Not My Candle</div>
	<div class='stats'><?php
		$pop = $cit->totalPopulation();
	$time = new dateTime($cit->getTime());
	$time = $time->format('Y-m-d-h-i-s');
	$html = "<div class='stats'>";
	$html .= "Iteration Time {$time} | Lit Candles {$pop}</div>";
		echo $html;
		?></div>
	<menu><div class='lfmn'><a href='map.php'>Map</a><a href='candles.php?status=1&page=1'>Lit Candles</a> <a href='candles.php?status=2&page=1'>Extinguished Cnadles</a></div><div class='rgmn'><a href='contribute.php'>Information</a><a href='index.php'>Back To Main</a></div></menu>
</div>