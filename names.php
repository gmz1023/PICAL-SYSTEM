<?php
include('./includes/db.php');
$filename="yob2015.txt";; $delimiter=',';

	if(!file_exists($filename) || !is_readable($filename))
		return FALSE;
	
	$header = NULL;
	$data = array();
	if (($handle = fopen($filename, 'r')) !== FALSE)
	{
		while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
		{
			if(!$header)
				$header = $row;
			else
				$data[] = array_combine($header, $row);
		}
		fclose($handle);
	}
echo "<pre>";
#print_r($data);
$int = count($data)-1;
$sql = "INSERT INTO first_names(name,gender) VALUES";
for($i = 0; $i <= $int; $i++)
{
	$gender = $data[$i]['Gender'];
	$sql.= "('{$data[$i]['Name']}', '{$gender}'),";
}
$sql = trim($sql, ',');

$que = $db->prepare($sql);
try { $que->execute(); echo "SUCCESS!"; } 
catch(PDOException $e) { echo $e->getMessage(); }

?>