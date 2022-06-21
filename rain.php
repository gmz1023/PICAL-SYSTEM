<?php
require_once('includes/db.php');
if(!isset($_SESSION['rc'])) { $_SESSION['rc'] = 0; } ;
	$cit = new base($db,0);
	echo "Starting Rain Cycle #{$_SESSION['rc']}  \n";
	$cit->rain();
$_SESSION['rc'] = $_SESSION['rc']+1;