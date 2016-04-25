<?php
	require_once "commonUtil.php";

	// Check the Date of Birth is match or not
    function isMatch($sid, $dob){
		$dbc = connectToDB();
		$query = "SELECT BIRTH FROM STUDENT WHERE ID=:sid";
		$stmt = $dbc->prepare($query);
		$stmt->bindParam(':sid',$sid,PDO::PARAM_STR);
		$stmt->execute();
		closeDB($dbc);
		if($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			$tTime = new DateTime($row["BIRTH"]);
			$iTime = new DateTime($dob);
			$tDate = $tTime->format('m/d/Y');
			$iDate = $iTime->format('m/d/Y');
			return $tDate == $iDate;
		}
		return false;
    }

	// Check the sid and dob are valid or not
	function isValid($sid, $dob){
		return isExist("STUDENT", "ID", $sid)
				&& isMatch($sid, $dob);
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
