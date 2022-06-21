<?php
	require_once('includes/db.php');
	$cit = new base($db,0);
?>
<html>
	<head>
		<title>Not My Candle Simulator</title>
	</head>
	<script src="jquery-3.6.0.min.js"></script>
	<script src="jquery-ui.min.js"></script>
	</script>
<link rel=stylesheet href="style/stylesheets/screen.css">
	<body>
		<div id='console'>
          <?php include('log.php'); ?>
			</div>
		<div id='registry'>
			<?php include('reg.php'); ?>
		</div>
	</body>
</html>