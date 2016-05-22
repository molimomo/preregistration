<?php
	function santitizeString($var){
		$var = trim($var);
		$var = stripslashes($var);
		$var = strip_tags($var);
		$var = htmlentities($var);
		return $var;
	}

	// Generate select menu for year
	function yearMenu(){
		$beginYear = 1950;
		$endYear = 2010;
		echo "<select name=\"year\">";
		for($i=$beginYear; $i<=$endYear; $i++){
			echo "<option value=\"".$i."\">".$i."</option>";
		}	
		echo "</select>";
	}

	// Generate select menu for month
	function monthMenu(){
		echo "<select name=\"month\">";
		for($i=1; $i<=12; $i++){
			echo "<option value=\"".$i."\">".jdmonthname(gregoriantojd($i,1,1), 0)."</option>";
		}	
		echo "</select>";
	}

	// Generate select menu for day
	function dayMenu(){
		echo "<select name=\"day\">";
		for($i=1; $i<=31; $i++){
			echo "<option value=\"".$i."\">".$i."</option>";
		}	
		echo "</select>";
	}
?>
