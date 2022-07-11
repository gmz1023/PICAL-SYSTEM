<?php
	require_once('includes/db.php');
	$cit = new base($db,0);
define('IS_ACTIVE',true);
?>
<html>
	<head>
		<title>Not My Candle Simulator</title>
	</head>
	<script src="jquery-3.6.0.min.js"></script>
	<script src="jquery-ui.min.js"></script>
<?php if(IS_ACTIVE == true)
{
?>
	<script type="text/javascript">
		$(document).ready(function(){
			
		$('#registry').load('reg.php');
		});
	</script>
<link rel=stylesheet href="style/stylesheets/screen.css">
	<body>
<?php include('parts/header.php'); ?>
		<div id='console'>
          <?php include('log.php'); ?>
			</div>
		<div class='regmenu'>
			<a id='cit'>Citizens</a> | <a id='grave' href='graveyard.php'>Graveyard</a>
		<div id='registry'>

		</div>
		</div>
<?php include('parts/footer.php'); 
}
	else
	{
		echo "<h1>Coming Soon!</h1>";
	}
	?>