<?php
/*
Database Constants
*/
error_reporting(E_ALL);
define("DB_HOST", 'localhost');
define("DB_USER", 'gmz1023');
define("DB_PASS", '');
define("DB_NAME", "lurch");
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
define("sleep_type", 'debug'); /* u uses usleep | s uses sleep default at s */
switch(sleep_state)
{
	case 'on':
		define("sleep_var", 1);
		define("msg_delay", 555000);
		define("TIME_CHOICE", mt_rand(1,30));
		break;
	case 'speedrun':
		define("sleep_var", 1000);
		define("msg_delay", 5);
		define("TIME_CHOICE", 40);
		break;
	case 'night':
		define("sleep_var", 0);
		define("msg_delay", 155000);
		define("TIME_CHOICE", 40);
		break;
	case 'debug':
		define("sleep_var", 3);
		define("msg_delay", 555000);
		define("TIME_CHOICE", 100);
		break;
	default:
		define("sleep_var", 1);
		define("msg_delay", 100000);
		define("TIME_CHOICE", 1);
	break;
}
	
/* Simulation Constants */
#define("TIME_STEP", '+13563 minutes');
define("weight_units", "g");
define("liquid_units", "l");
define("water_consumption",2000*TIME_CHOICE);
define("food_consumption", 4.5*TIME_CHOICE);
define("average_global_temp", 74);
define("max_local_temp", 140);
define("min_local_temp", -130);
define("bio_temp_max",100);
define("bio_temp_min",0);
define("TIME_STEP", "+".TIME_CHOICE." day");
?>
