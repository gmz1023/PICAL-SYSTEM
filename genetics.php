<?php
/*

		Look into why the mt_rand is printing 0 for the majority of the loops

*/
$sql = "SELECT 
			c.cid,
			g.genome as seq
		FROM
			citizens as c,
			genetics as g
		WHERE
			c.cid = g.cid;
			";
$que = $db->prepare($sql);
	$cit = new base($db,0);
if(!isset($_SESSION['mc'])) { $_SESSION['mc'] = 0; } ;
$chance = mutation_chance/100;
try { 
		$que->execute();
		while($row = $que->fetch(PDO::FETCH_ASSOC))
		{
			mt_srand($_SESSION['mc']+$row['cid']);
			$chance = mt_rand(0,$chance);
			if($chance == 1) { 

				echo "MUTATION HAS OCCURED \n";
				$cit->mutate($row['seq'],$row['cid']); 
			}
			usleep(100);
		}
	}catch(PDOException $e) { die($e->getMessage());}
echo "New Mutation Loop #{$_SESSION['mc']} \n";
$_SESSION['mc'] = $_SESSION['mc']+1;



?>