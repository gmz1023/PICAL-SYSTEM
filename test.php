<?php
require_once('includes/db.php');
$cit = new base($db,1);
for($i = 1; $i++; $i < 10)
{
$cit->Rain();
}