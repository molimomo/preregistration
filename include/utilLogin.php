<?php
	// Include helper functions for login.php
	require_once "commonUtil.php";
	require_once 'include/DBManagement_PDO.php';
	
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

	// Check whether student already submitted pre-regiatration.
	function isRegistered($sid){
		$dbc = connectToDB();
		$query = "SELECT * 
					FROM PRE_REGISTER 
					WHERE StudentID=(SELECT StudentID 
										FROM STUDENT
										WHERE ID=:sid)";
		$stmt = $dbc->prepare($query);
		$stmt->bindParam(':sid',$sid,PDO::PARAM_STR);
		$stmt->execute();
		closeDB($dbc);
		return ($row = $stmt->fetch(PDO::FETCH_ASSOC));
	}
?>
