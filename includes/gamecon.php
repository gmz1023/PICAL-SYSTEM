<?php
//Old Aslo Site Required Constants
#define("DB_STRING", 'mysql:host=localhost;dbname=my_guitar_shop1');
/* Extra Information 
	2 = Everything shown (Death Messages, Infected, Pregenancy)
	1 = Some things shown
	0 = Only Basic Information Show (Date/Pop/SimLength)
	
*/
define("GAY_SEX", 'on');
define("extra_info",100);
/* 
	Citizen Standards
*/



/* Sleep State 
	Turn off if you want to have quick simulations [IE For debugging or general data gathering]
	turn on if you plan on watching longer simulations (IE will be running for a while)
	Change Sleep Var to effect time between sleeping (in seconds). Default is: 1 
	
	if changing the type == it switchs from seconds to milliseconds (or micro? nano? FIK);
	1 second = 1000000;
*/

define("sleep_state", 'speedrun'); /* On/Off */
define("sleep_type", 'off'); /* u uses usleep | s uses sleep default at s */
define("sleep_var", 0);
define("msg_delay", 0);
define("TIME_CHOICE", 7);
/* Simulation Constants */
#define("TIME_STEP", '+13563 minutes');
define("weight_units", "g");
define("liquid_units", "l");
define("water_consumption",3*TIME_CHOICE);
define("food_consumption", 1000*TIME_CHOICE);
define("average_global_temp", 74);
define("max_local_temp", 140);
define("min_local_temp", -130);
define("bio_temp_max",100);
define("bio_temp_min",0);
define("GENE_MAX",250);
define('mutation_chance',1000);
define("TIME_STEP", "+".TIME_CHOICE." day".mt_rand(0,86400)." seconds");

?>