<?php
include('includes/db.php');
$fn = 'pierson';
$mn = 'fountaine';
$cit = new base($db,0);

echo $cit->prettyName(1);