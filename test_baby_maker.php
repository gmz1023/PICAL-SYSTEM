<?php
require_once('includes/db.php');
$cit = new base($db);

$cit->newCitizens(1,2,100);