<?php
$fn = 'pierson';
$mn = 'fountaine';
for($i = 0; $i < 100; $i++)
{
$name = substr($fn,mt_rand(1,strlen($fn)),mt_rand(1,strlen($fn)));
$name .= substr($mn,mt_rand(1,strlen($mn)),mt_rand(1,strlen($mn)));
if(strlen($name) < 5 )
{
	$name = mt_rand(0,1) == 1 ? $fn : $mn;
}
else
{
	$name = $name;
}
	echo $name."<br/>";
}