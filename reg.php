<?php
$sql = "SELECT first_name, last_name, status,cid FROM citizens WHERE status > 0";
$que = $db->prepare($sql);

try { 
	$que->execute();
	$html = "<ul>
	<li>Key
		<ul>
			<li>Green | Alive!</li>
			<li>Black | Dead!</li>
		</ul>";
	while($row = $que->fetch(PDO::FETCH_ASSOC))
	{
		$status = $row['status'] > 0 ? 'alive' : 'dead';
		$html .= "<li ><a class='{$status}' href='history.php?cid={$row['cid']}'>".$row['first_name']." ".$row['last_name']."</a></li>";
	}
	$html .= "</ul>";
}catch(PDOException $e) { die($e->getMessage());}
echo $html;