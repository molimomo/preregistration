<?php
	require_once "commonUtil.php";
	require_once "valueMapping.php";
	function getCourseInfo($cid){
		global $program;
		$dbc = connectToDB();
		$query = "SELECT TITLE, DESCRIPTION, CORE, AREA, PREREQUISITE
					FROM ALL$ 
					WHERE CODE=:cid";
		$stmt = $dbc->prepare($query);
		$stmt->bindParam(":cid",$cid,PDO::PARAM_STR);
		$stmt->execute();
		closeDB($dbc);
		if($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			$results["TITLE"] = $row["TITLE"];
			$results["DESCRIPTION"] = $row["DESCRIPTION"];
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

	function getCurQuery(){
		return "SELECT DISTINCT reg.CLASS, reg.GRADE, course.STARTS, course.TITLE 
					FROM STUDENT AS stu, REGISTER AS reg, COURSE AS course 
					WHERE reg.FIRSTNAME=:firstname
						AND reg.LASTNAME=:lastname 
						AND reg.CLASS=course.CLASS
						AND reg.STARTS=course.STARTS
					 	AND reg.GRADE=''
				 		ORDER BY course.STARTS";
	}

	function getTakenQuery(){
		return "SELECT DISTINCT reg.CLASS, reg.GRADE, course.STARTS, course.TITLE 
					FROM STUDENT AS stu, REGISTER AS reg, COURSE AS course 
					WHERE reg.FIRSTNAME=:firstname
						AND reg.LASTNAME=:lastname 
						AND reg.CLASS=course.CLASS
						AND reg.STARTS=course.STARTS
					 	AND reg.GRADE!=''
				 		ORDER BY course.STARTS";
	}

	function getWaivedQuery(){
		return "SELECT CLASS, EXAMINE, WAIVED_DATE
					FROM COURSEWAIVED
					WHERE FIRSTNAME=:firstname
						AND LASTNAME=:lastname";
	}
	
	function getLevel($cid){
		// Check the last character of class id
		if(!is_numeric(substr($cid,-1)))
			return 500;
		
		// Check the last 3 characters
		return (intval(substr($cid,-3))>400)?500:300;	
	}

	function displayHistory(){
		displayCourse(DISPLAY_CUR);
		displayCourse(DISPLAY_TAKEN);
		displayCourse(DISPLAY_WAIVED);
	}

	function displayCurrentCourse(){
		displayCourse(DISPLAY_CUR);
	}
	
	function displayTakenCourse(){
		displayCourse(DISPLAY_TAKEN);
	}

	function displayWaivedCourse(){
		displayCourse(DISPLAY_WAIVED);
	}

	function displayCourse($display){
		global $firstname, $lastname;
		$dbc = connectToDB();
		$query = "";
		$header = "";
		$condition = "";
		switch($display){
			case DISPLAY_CUR:
				$header = "Current";
				$query = getCurQuery();
				break;
			case DISPLAY_TAKEN:
				$header = "Taken";
				$query = getTakenQuery();
				break;
			case DISPLAY_WAIVED:
				global $waivedType;
				$header = "Waived";
				$query = getWaivedQuery();
				break;
			default:
				exit("Invalid display option!");
				break;
		}
		$stmt = $dbc->prepare($query);
		$stmt->bindParam(":firstname",$firstname,PDO::PARAM_STR);
		$stmt->bindParam(":lastname",$lastname,PDO::PARAM_STR);
		$stmt->execute();
		closeDB($dbc);	
		echo "<h3>".$header." Courses</h3>
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
			$info = getCourseInfo($row["CLASS"]);
        	echo "<tr align=\"left\">";
            echo "<td align=\"center\">".$row["CLASS"]."</td>";
            echo "<td>".$info["TITLE"]."</td>";
			if($display==DISPLAY_WAIVED){
				echo "<td align=\"center\">".$waivedType[intval($row["EXAMINE"])]."</td>";
            	echo "<td align=\"center\">".dateConvert($row["WAIVED_DATE"])."</td>";
			}
			else{
				echo "<td align=\"center\">".$row["GRADE"]."</td>";
            	echo "<td align=\"center\">".dateConvert($row["STARTS"])."</td>";
			}
            echo "<td>".$info["CORE"]."</td>";
            echo "<td align=\"center\">".$info["AREA"]."</td>";
            echo "<td>".$info["PREREQUISITE"]."</td>";
            echo "</tr>";
        }
        echo "</table>";
	}

	function displayStudentInfo(){
		global $sid, $dob, $firstname, $lastname, $program, $preEducation, $preDegree;
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
			echo "<h2>Profile</h2>
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
					<td>".$preEducation[$result["PRE_EDUCATION"]]."</td>
				</tr>
				<tr align=\"left\">
					<th><strong>Cohort</strong></th>
					<th><strong>Occupation</strong></th>
					<th><strong>Degree(s) Earned</strong></th>
				</tr>
				<tr align=\"left\">
					<td>".$result["COHORT"]."</td>
					<td>".$result["COMPANY"]."</td>
					<td>".$preDegree[$result["PRE_DEGREE"]]."</td>
				</tr>
				<tr align=\"left\">
					<th colspan=\"3\"><strong>Address</strong></th>
				</tr>
				<tr align=\"left\">
					<td colspan=\"3\">".$result["STREET"].", "
						.$result["CITY"].", ".$result["STATE"].", ".$result["ZIP"]."</td>
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
