<?php
	require_once "commonUtil.php";
	function getCourseInfo($cid){
		global $program;
		$dbc = connectToDB();
		$query = "SELECT CORE, AREA, PREREQUISITE
					FROM ALL$ 
					WHERE CODE=:cid";
		$stmt = $dbc->prepare($query);
		$stmt->bindParam(":cid",$cid,PDO::PARAM_STR);
		$stmt->execute();
		closeDB($dbc);
		if($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			$results["CORE"] = (strpos($row["CORE"],$program)!==false)?'V':"";
			$results["AREA"] = (strpos($row["AREA"],$program)!==false)?'V':"";
			$results["PREREQUISITE"] = $row["PREREQUISITE"];
			return $results;
		}
		else{
			exit("Cannot Find Course Information!");
		}
	}

	function dateConvert($datetime){
		$newDate = new DateTime($datetime);
		$date = $newDate->format("m/d/Y");
		return $date;
	}

	function displayTakenCourse(){
		global $firstname, $lastname;
		$dbc = connectToDB();
		$query = "SELECT DISTINCT reg.CLASS, reg.GRADE, course.STARTS, course.TITLE 
					FROM STUDENT AS stu, REGISTER AS reg, COURSE AS course 
					WHERE reg.FIRSTNAME=:firstname
						AND reg.LASTNAME=:lastname 
						AND reg.CLASS=course.CLASS
						AND reg.STARTS=course.STARTS
					ORDER BY course.STARTS";
		$stmt = $dbc->prepare($query);
		$stmt->bindParam(":firstname",$firstname,PDO::PARAM_STR);
		$stmt->bindParam(":lastname",$lastname,PDO::PARAM_STR);
		$stmt->execute();
		closeDB($dbc);	
		echo "<h3>Taken Courses</h3>
				<table width=\"100%\" align=\"left\" border=\"1\">
				<tr  align=\"left\">
					<th align=\"center\">Class</th>
					<th align=\"center\">Title</th>
					<th align=\"center\">Grade</th>
					<th align=\"center\">Date</th>
					<th align=\"center\">Core</th>
					<th align=\"center\">Area</th>
					<th align=\"center\">Pre-requisite</th>
				</tr>";
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        	echo "<tr align=\"left\">";
            echo "<td align=\"center\">".$row["CLASS"]."</td>";
            echo "<td>".$row["TITLE"]."</td>";
            echo "<td align=\"center\">".$row["GRADE"]."</td>";
            echo "<td align=\"center\">".dateConvert($row["STARTS"])."</td>";
			$info = getCourseInfo($row["CLASS"]);
            echo "<td>".$info["CORE"]."</td>";
            echo "<td align=\"center\">".$info["AREA"]."</td>";
            echo "<td>".$info["PREREQUISITE"]."</td>";
            echo "</tr>";
        }
        echo "</table>";
	}

	function displayStudentInfo(){
		global $sid, $dob, $firstname, $lastname, $program;
		$dbc = connectToDB();
		$query = "SELECT * FROM STUDENT WHERE ID=:sid";
		$stmt = $dbc->prepare($query);
		$stmt->bindParam(":sid",$sid,PDO::PARAM_STR);
		$stmt->execute();
		closeDB($dbc);	
		if($result = $stmt->fetch(PDO::FETCH_ASSOC)){
			// Extract values from database and store them into global variables
			$firstname = $result["FIRSTNAME"];
			$lastname = $result["LASTNAME"];
			$program = $result["PROGRAM"];

			// Display Student's Personal Information
			echo "<h2>Student Profile</h2>
				<table width=\"100%\" align=\"left\">
				<tr align=\"left\">
					<th><strong>First Name</strong></th>
					<th><strong>Last Name</strong></th>
					<th><strong>Program</strong></th>
				</tr>
				<tr align=\"left\">
					<td>".$firstname."</td>
					<td>".$lastname."</td>
					<td>".$program."</td>
				</tr>
				<tr align=\"left\">
					<th><strong>Date of Birth (mm/dd/yyyy)</strong></th>
					<th><strong>Primary Phone</strong></th>
					<th><strong>Email</strong></th>
				</tr>
				<tr align=\"left\">
					<td>".$dob."</td>
					<td>".$result["H_PHONE"]."</td>
					<td>".$result["EMAIL"]."</td>
				</tr>
				<tr align=\"left\">
					<th><strong>Student ID</strong></th>
					<th><strong>Gender</strong></th>
					<th><strong>Education Level</strong></th>
				</tr>
				<tr align=\"left\">
					<td>".$result["ID"]."</td>
					<td>".$result["MR_MS"]."</td>
					<td>".$result["PRE_EDUCATION"]."</td>
				</tr>
				<tr align=\"left\">
					<th colspan=\"3\"><strong>Address</strong></th>
				</tr>
				<tr align=\"left\">
					<td>".$result["STREET"]."</td>
				</tr>
				<tr align=\"left\">
					<th><strong>City</strong></th>
					<th><strong>State</strong></th>
					<th><strong>Zip Code</strong></th>
				</tr>
				<tr align=\"left\">
					<td>".$result["CITY"]."</td>
					<td>".$result["STATE"]."</td>
					<td>".$result["ZIP"]."</td>
				</tr>
				<tr align=\"left\">
					<th><strong>Cohort</strong></th>
					<th><strong>Occupation</strong></th>
					<th><strong>Degree(s) Earned</strong></th>
				</tr>
				<tr align=\"left\">
					<td>".$result["COHORT"]."</td>
					<td>".$result["COMPANY"]."</td>
					<td>".$result["PRE_DEGREE"]."</td>
				</tr>
			</table>";	
		}
		else{
			exit("Cannot find Student Information");
		} 
	}
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
