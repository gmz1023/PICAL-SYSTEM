<?php
require_once('includes/db.php');
$sql = "SELECT * FROM console ORDER BY cid DESC LIMIT 500";
$que = $db->prepare($sql);
try { 
	$que->execute();
	$array = [];
	$time = new dateTime($cit->getTime());
	$time = $time->format('Y-m-d-h-i-s');
	$html = "<div class='log_con'>";
	while($row = $que->fetch(PDO::FETCH_ASSOC))
	{
		$array[] = array('cid'=>$row['cid'],'color'=>$row['color'],'text'=>$row['text']);
		$html .= "<div class='msg {$row['color']}'><span class='label'>[{$row['cid']}]</span> ".$row['text'].'</div>';
	}
	$html .= "</div>";
	/*
		Uncomment below for json mode
	*/
	//echo  json_encode($array);
		

}catch(PDOException $e) { die($e->getMessage()); }
echo $html;

?>
