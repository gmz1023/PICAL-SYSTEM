<?php
spl_autoload_register(function ($name) {
	$file = $name.'.php';

		$path = "./includes/class/".$file;
try {
   	if (file_exists($path)) {
       require_once($path);
	  
   	} else {
       die("The file {$path} could not be found! \n");

   	}
}catch(exception $e) { echo $e->getMessage();}
});
