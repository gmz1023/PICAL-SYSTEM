<?php
/*
Database Constants
*/
error_reporting(E_ALL);
define("DB_HOST", 'localhost');
define("DB_USER", 'root');
define("DB_PASS", '');
define("DB_NAME", "lurch");
define("DB_STRING", 'mysql:host=localhost;dbname=my_guitar_shop1');
/* Extra Information 
	2 = Everything shown (Death Messages, Infected, Pregenancy)
	1 = Some things shown
	0 = Only Basic Information Show (Date/Pop/SimLength)
	
*/
define("GAY_SEX", 'on');
define("extra_info",9);
/* Sleep State 
	Turn off if you want to have quick simulations [IE For debugging or general data gathering]
	turn on if you plan on watching longer simulations (IE will be running for a while)
	Change Sleep Var to effect time between sleeping (in seconds). Default is: 1 
	
	if changing the type == it switchs from seconds to milliseconds (or micro? nano? FIK);
	1 second = 1000000;
*/
define("sleep_state", 'debug'); /* On/Off */
define("sleep_type", 's'); /* u uses usleep | s uses sleep default at s */
switch(sleep_state)
{
	case 'on':
		define("sleep_var", 1);
		define("msg_delay", 555000);
		define("TIME_CHOICE", mt_rand(-1,30));
		break;
	case 'night':
		define("sleep_var", 0);
		define("msg_delay", 155000);
		define("TIME_CHOICE", 14);
		break;
	case 'debug':
		define("sleep_var", 0);
		define("msg_delay", 50000);
		define("TIME_CHOICE", mt_rand(0,30));
		break;
	default:
		define("sleep_var", 0);
		define("msg_delay", 0);
		define("TIME_CHOICE", 10);
	break;
}
	
/* Simulation Constants */
#define("TIME_STEP", '+13563 minutes');

define("TIME_STEP", "+".TIME_CHOICE." day");
?>