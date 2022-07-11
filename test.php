<?php
$sid = 1;
echo "<pre>";
for($sid = 1; $sid < 360; $sid++)
{
		$u = ($sid-18) > 1 ? ($sid-18) : 360;
		$d = ($sid+18) < 360 ? ($sid+18) : 1;
		$l = ($sid+1) < 360 ? ($sid+1) : 360;
		$r = ($sid-1) > 1 ? ($sid-1) : 1;
		$ar = array('sid'=>$sid,'up'=>$u,'down'=>$d,'left'=>$l,'right'=>$r);
	print_r($ar);
	echo "<br />";
}
		//shuffle($ar);

		$move = $ar[0];
		$move = $move > 360 ? 1 : $move;
		$move = $move < 1 ? 360 : $move;