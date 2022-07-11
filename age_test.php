<?php
include('includes/db.php');
$cit = new base($db,0);

echo $cit->citizenAge(1,2);