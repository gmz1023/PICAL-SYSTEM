<?php
include('includes/db.php');
if(!isset($_GET['meth']))
{
	//* Pagination Stuff I am terrible at, Thanks code-boxx
	define("PER_PAGE", "150"); // ENTRIES PER PAGE
	$stmt = $db->prepare("SELECT CEILING(COUNT(*) / ".PER_PAGE.") `pages` FROM `gravestones`");
	$stmt->execute(); 
	$pageTotal = $stmt->fetch(PDO::FETCH_COLUMN);
	// (C) GET ENTRIES FOR CURRENT PAGE
// (C1) LIMIT (X, Y) FOR SQL QUERY
	$pageNow = isset($_GET["page"]) ? $_GET["page"] : 1 ;
	$limX = ($pageNow - 1) * PER_PAGE;
	$limY = PER_PAGE;
	// * Actual Rest Of the Owl
$sql = "SELECT cid, first_name, last_name, gender FROM gravestones ORDER BY cid LIMIT {$limX}, {$limY}";
$que = $db->prepare($sql);
$html = "<table><tr colspan='3'><th>Dead Citizens</th></tr>";
$html .= "<tr><th>first name</th><th>Last Name</th><th>Gender</th></tr>";
try { $que->execute();
	 while($row = $que->fetch(PDO::FETCH_ASSOC))
	 {
		 $html .= "<tr><td>{$row['first_name']}</td><td>{$row['last_name']}</td><td>{$row['gender']}</td><td><a href='graveyard.php?meth=res&cid={$row['cid']}'>Ressurect</a></td></tr>";
	 }
	 $html .= "</table>";
	}catch(PDOException $e) { die($e->getMessage());}
echo $html;
?>
<div class="pagination" id="pagination"><?php
for ($i=1; $i<=$pageTotal; $i++) {
  printf("|<a %shref='1-paginate.php?page=%u'>%u</a>|", 
    $i==$pageNow ? "class='current' " : "", $i, $i
  );
}
?></div>

<?php
}
else
{
	if(!isset($_GET['cid']) || !is_numeric($_GET['cid']))
	{
		header('location:graveyard.php');
	}
	$cid = $_GET['cid'];
	switch($_GET['meth'])
	{
		case 'res':
			$sql = "INSERT INTO citizens SELECT * FROM gravestones WHERE cid = {$cid};";
			$sql2 = "DELETE FROM gravestones WHERE cid = {$cid}";
			$sql3 = "INSERT INTO genetics SELECT * FROM dead_dna WHERE cid = {$cid}";
			$sql4 = "DELETE FROM dead_dna WHERE cid = {$cid}";
			try {
				$db->beginTransaction();
				$db->exec($sql);
				$db->exec($sql2);
				$db->exec($sql3);
				$db->exec($sql4);
				$db->commit();
				header("location:graveyard.php");
			}catch(PDOException $e) { die($e->getMessage());
									$db->rollBack();
									}
		default:
			header("location:graveyard.php");
			break;
	}
}