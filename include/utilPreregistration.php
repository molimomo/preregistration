<?php
	require_once "commonUtil.php";
	require_once "valueMapping.php";


	// Convert Time to Slot 
	// (Morning, Evening, Night, N/A)
	function convertToSlot($time){
		if(empty(trim($time))){
			return "N/A";
		}
		$time = substr($time, 0, -2); 
		$slot = explode("-",$time);
		$frame = array();
		for($i=0;$i<count($slot);$i++){
			$tmp = explode(":",$slot[$i]);
			array_push($frame,  intval($tmp[0].$tmp[1]));
		}
		if($frame[1]>=1200){
			return "Morning";
		}
		else{
			$frame[0]+=1200;
			$frame[1]+=1200;
			if($frame[0] > $frame[1]){
				return($frame[0]>=2400)?"Evening":"Morning";
			}
			else{
				return($frame[0]>=1700)?"Night":"Evening";
			}
		}
	}

	// Check Couses Time Conflict
	function checkConflict($selected){
		$courseTime = array();
		for($i=0;$i<count($selected);$i++){
			$selInfo = explode("/",$selected[$i]);
			$courseTime[$i] = $selInfo;
		}

		for($i=0;$i<count($selected)-1;$i++){
			for($j=$i+1;$j<count($selected);$j++){
				if($courseTime[$i][1] == $courseTime[$j][1]){
					$slot1 = convertToSlot($courseTime[$i][2]);
					$slot2 = convertToSlot($courseTime[$j][2]);
					if(($slot1 == $slot2) && ($slot1!="N/A")){
						return $selected[$i]." & ".$selected[$j];
					}
				}
			}
		}
		return "";
	}

	// Get Note for student's preregistration form
	function getNotes($cid){
		global $curCourse, $takenCourse, $waivedCourse;
		$notes ="";
		// Check Current Course
		if(isset($curCourse[$cid])){
			$notes .= "Enrolling<br>";
		}	

		// Check Taken Courses
		if(isset($takenCourse[$cid])){
			$notes .= "Taken<br>";
		}

		// Check Waived Course
		if(isset($waivedCourse[$cid])){
			$notes .= "Waived<br>";
		}

		// Check Distance Learning Course
		if($cid[strlen($cid)-1] === 'X'){
			$notes .= "Distance Learning";
		}
		
		return $notes;
	}

	// Display a single course information in pre-registration form
	function listCourseInfo($row){
		$info = getCourseInfo($row["CLASS"]);
		echo "<tr><td align=\"center\"><input type=\"checkbox\" name=\"selectedCourse[]\" 
			value=\"".$row["CLASS"]."/".$row["DAYS"]."/".$row["TIMES"]."/".$row["TUITION"]."/".$row["TITLE"]."/".$row["CourseID"]."\"></td>".
			"<td align=\"center\">".$row["CLASS"]."</td>". 
			"<td align=\"center\">".$row["DAYS"]."</td>". 
			"<td align=\"center\">".dateConvert($row["STARTS"])."</td>". 
			"<td align=\"center\">".$row["TIMES"]."</td>". 
			"<td>".$row["TITLE"]."</td>". 
			"<td align=\"center\">".$info["CORE"]."</td>". 
			"<td align=\"center\">".$info["AREA"]."</td>". 
			"<td>".$info["PREREQUISITE"]."</td>".
			"<td align=\"center\" id=\"notes\">".getNotes($row["CLASS"])."</td><tr>";	
	}

	// Display Pre-Registration Form
	function displayPreregistrationForm(){
		global $firstname, $lastname, $sid, $dob,$status, $studentID;
		$semester = SEMESTER;
		$dbc = connectToDB();
		$query = "SELECT *
					FROM COURSE
					WHERE SEMESTER=:semester
					ORDER BY TIMES";
		$stmt = $dbc->prepare($query);
		$stmt->bindParam(":semester",$semester,PDO::PARAM_STR);
		$stmt->execute();
		closeDB($dbc);
		echo "<h3>Pre-registration Courses</h3>";
		echo "<form id=\"registrationForm\" method=\"post\" action=\"studentInfo.php\">";
		echo "<table width=\"100%\" align=\"left\" border=\"1\">";
		echo "	<tr>
					<th align=\"center\"><strong>Check</strong></th>
					<th align=\"center\"><strong>Class</strong></th>
					<th align=\"center\"><strong>Days</strong></th>
					<th align=\"center\"><strong>Starts</strong></th>
					<th align=\"center\"><strong>Times</strong></th>
					<th align=\"center\"><strong>Title</strong></th>
					<th align=\"center\"><strong>Core</strong></th>
					<th align=\"center\"><strong>Area</strong></th>
					<th align=\"center\"><strong>Pre-requisite</strong></th>
					<th align=\"center\"><strong>Notes</strong></th>
				</tr>";
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			listCourseInfo($row);	
		} 
		echo "<tr>
				<td align=\"center\">Comments:</td>
				<td colspan=\"4\"><textarea name=\"comments\" id=\"comments\" cols=\"75\" rows=\"5\"></textarea></td>
				<td colspan=\"5\"><b><u>This form does not confirm your registration to the selected courses: please await confirmation via email from an Academic Advisor or Administrative Officer. If you still have question, please contact KSI Office: office@ksi.edu, (847) 679-3135</u></b></td>
			   </tr>
			<tr>
				<td colspan=\"10\">Considering changing major?
					Yes<input type=\"radio\" name=\"changeMajor\" value=\"1\"> 
					No<input type=\"radio\" name=\"changeMajor\" value=\"0\" checked=\"checked\"> 
					</td>
				</tr>
				<tr>
					<td colspan=\"10\" align=\"center\"><input type=\"submit\" name=\"submit\" id=\"submit\" value=\"submit\"></td>
				</tr>";
		echo "</table>
		<input type=\"hidden\" name=\"firstname\" value=\"".$firstname."\"".">".
		"<input type=\"hidden\" name=\"lastname\" value=\"".$lastname."\"".">".
		"<input type=\"hidden\" name=\"studentID\" value=\"".$studentID."\"".">".
		"<input type=\"hidden\" name=\"status\" value=\"".$status."\"".">".
		"<input type=\"hidden\" name=\"sid\" value=\"".$sid."\"".">".
		"<input type=\"hidden\" name=\"dob\" value=\"".$dob."\"".">".
		"</form>";
	}

	// Get Extra Course Information (CORE, AREA, DESCRIPTION)	
	function getCourseInfo($cid){
		global $program;
		$dbc = connectToDB();
		$query = "SELECT *
					FROM COURSE_CATALOG 
					WHERE CODE=:cid";
		$stmt = $dbc->prepare($query);
		$stmt->bindParam(":cid",$cid,PDO::PARAM_STR);
		$stmt->execute();
		closeDB($dbc);
		if($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			$row["CORE"] = (strpos($row["CORE"],$program)!==false)?'V':"";
			$row["AREA"] = (strpos($row["AREA"],$program)!==false)?'V':"";
			return $row;
		}
	}

	// Convert datetime format in MSSQL to m/d/y
	function dateConvert($datetime){
		$newDate = new DateTime($datetime);
		$date = $newDate->format("m/d/Y");
		return $date;
	}

	// Get Query for selecting current courses
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
	
	
	// Get Query for selecting taken courses
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

	// Get Query for selecting waived courses
	function getWaivedQuery(){
		return "SELECT CLASS, EXAMINE, WAIVED_DATE
					FROM COURSEWAIVED
					WHERE FIRSTNAME=:firstname
						AND LASTNAME=:lastname";
	}

	// Get Course Level	
	/*function getLevel($cid){
		// Check the last character of class id
		if(!is_numeric(substr($cid,-1)))
			return 500;
		
		// Check the last 3 characters
		return (intval(substr($cid,-3))>400)?500:300;	
	}*/
	function getLevel(){

	}

	function displayRecords(){
		global $curCourse, $takenCourse, $waivedCourse;
		echo "current records:<br>";
		foreach($curCourse as $course=>$grade){
			echo $course." - ".$grade."<br>";
		}
		echo "taken records:<br>";
		foreach($takenCourse as $course=>$grade){
			echo $course." - ".$grade."<br>";
		}
		echo "waived records:<br>";
		foreach($waivedCourse as $course=>$grade){
			echo $course." - ".$grade."<br>";
		}
	}		

	// Display student's course history
	function displayHistory(){
		global $curCourse, $takenCourse, $waivedCourse;
		$curCourse = displayCourse(DISPLAY_CUR);
		$takenCourse = displayCourse(DISPLAY_TAKEN);
		$waivedCourse =  displayCourse(DISPLAY_WAIVED);
	}

	// General Function to display Courses 
	function displayCourse($display){
		global $firstname, $lastname;
		$record = array();
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
		echo "<h3>".$header." Courses</h3>";
		if($stmt->rowCount()){
				echo"<table width=\"100%\" align=\"left\" border=\"1\">
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
						$record[$row["CLASS"]] = $waivedType[intval($row["EXAMINE"])];
						echo "<td align=\"center\">".$waivedType[intval($row["EXAMINE"])]."</td>";
						echo "<td align=\"center\">".dateConvert($row["WAIVED_DATE"])."</td>";
					}
					else{
						$record[$row["CLASS"]] = $row["GRADE"];
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
		else{
			echo "<p>N/A</p><br>";
		}
		return $record;
	}

	// Display student's personal information 
	function displayStudentInfo($sid, $dob){
		global $firstname, $lastname, $program, $preEducation, $preDegree, $status, $studentID;
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
			$status = $result["STATUS"];
			$studentID = $result["StudentID"];
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
?>
