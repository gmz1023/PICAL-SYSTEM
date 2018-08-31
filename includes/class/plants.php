<?php
class plants extends movement
{
	function killPlants()
	{
		$sql = "UPDATE map SET plants = plants-".mt_rand(0,1)." WHERE temp > 100";
	}
}