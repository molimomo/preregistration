<?php
	/*********************************************
		Include the value mapping in database
	**********************************************/
	require_once "include/DBManagement_PDO.php";
	require_once "valueMapping.php";
	require_once "commonUtil.php";

	/*********************************************
		Helper function for studentInfo.php page
	**********************************************/

	// Check prerequisite courses with "or" delimiter
	function checkPreOr($courses){
		$pCourses = explode(" or ", $courses);
		for($i=0;$i<count($pCourses);$i++){
			if(isInHistory($pCourses[$i])){
				return "";
			}
		}
		return $courses;
	}

	// Check prerequisite courses with "and" delimiter
	function checkPreAnd($courses){
		$pCourses = explode(" and ", $courses);
		for($i=0;$i<count($pCourses);$i++){
			if(!isInHistory($pCourses[$i])){
				return $courses;
			}
		}
	}

	// Check student's history with prerequisite courses
	function isInHistory($course){
		global $takenCourse, $waivedCourse;
		//echo "# of waived: ".count($waivedCourse)."<br>";
		// Check Waived Course and Taken Courses
		/*if(array_key_exists($course, $waivedCourse)){
			echo "exist in waived!<br>";
		}
		if(array_key_exists($course, $takenCourse)){
			echo "exist in taken!<br>";
		}*/
		return array_key_exists($course, $waivedCourse) 
				|| array_key_exists($course, $takenCourse);	
	}

	// Check Prerequitsite Courses
	function checkPrerequitsite($code){
		$dbc = connectToDB();
		$query = "SELECT PREREQUISITE
					FROM COURSE_CATALOG
					WHERE CODE=:code";
		$stmt = $dbc->prepare($query);
		$stmt->bindParam(':code', $code);
		$result = "";

		$stmt->execute();
		if($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			$prerequisite = trim($row["PREREQUISITE"]);
			if(strpos($prerequisite," or ")!==false){
				$result .= checkPreOr($prerequisite);
			}
			else if(strpos($prerequisite," and ")!==false){
				$result .= checkPreAnd($prerequisite);
			}
			else if(!empty($prerequisite)){
				$result .= isInHistory($prerequisite)?"":$prerequisite;	
			}
		}
		else{
			//echo " - Got nothing!<br>";
		}
		closeDB($dbc);
		return empty($result)?"":"You have to take ".$result." prerequisite first!<br>";
	}	


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
		echo "In check Conflict!<br>";
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
						return $courseTime[$i][0]."/".$courseTime[$i][1]."/".$courseTime[$i][2].
								" and ".$courseTime[$j][0]."/".$courseTime[$j][1]."/".$courseTime[$j][2];
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
		if(isOnline($cid)){
			$notes .= "Distance Learning<br>";
		}
		
		return $notes;
	}
	
	// Check Course is online or not
	function isOnline($cid){
		return ($cid[strlen($cid)-1] === 'X');
	}

	// Check visa status
	function isInternational(){
		global $status, $statusMap;
		return strcmp($statusMap[$status],"GREEN CARD") != 0;
	}

	// Check the ability of courses
	// 0: Available
	// 1: International student cannot select online courses
	// 2: Student has undone prerequisite courses
	function checkAvailability($course){
		$result ="";
		// Check visa status and course type
		if(isInternational() && isOnline($course)){
			$result .= "You cannot take this Online Course!<br>";
		}	
		// Check prerequistise
		$result .= checkPrerequitsite($course);
		return $result;	
	} 

	// Display a single course information in pre-registration form
	function listCourseInfo($row){
	//	echo "In list Course Info!<br>";
		global $status, $statusMap;
		//$isInternational = (strcmp($statusMap[$status],"GREEN CARD") != 0);
		$info = getCourseInfo($row["CLASS"]);
		//if( ($isInternational === true) && (isOnline($row["CLASS"]))){
		$availabilityRes = checkAvailability($row["CLASS"]);
		if(!empty($availabilityRes)){
			echo "<tr><td></td>" ;
		}
		else{
			echo "<tr><td align=\"center\"><input type=\"checkbox\" 
				name=\"selectedCourse[]\" value=\"".$row["CLASS"]."/".$row["DAYS"].
				"/".$row["TIMES"]."/".$row["TUITION"].
				"/".$row["TITLE"]."/".$row["CourseID"]."\"></td>";
		}
		echo "<td align=\"center\">".$row["CLASS"]."</td>". 
			"<td align=\"center\">".$row["DAYS"]."</td>". 
			"<td align=\"center\">".dateConvert($row["STARTS"])."</td>". 
			"<td align=\"center\">".$row["TIMES"]."</td>". 
			"<td>".$row["TITLE"]."</td>". 
			"<td align=\"center\">".$info["CORE"]."</td>". 
			"<td align=\"center\">".$info["AREA"]."</td>". 
			"<td>".$info["PREREQUISITE"]."</td>".
			"<td align=\"center\" id=\"notes\" >".
			getNotes($row["CLASS"])."<span style=\"color:red\">".$availabilityRes."</span></td><tr>";	
	}

	// Display Pre-Registration Form
	function displayPreregistrationForm(){
		global $firstname, $lastname, 
				$sid, $dob,$status, 
				$studentID;
		$semester = SEMESTER;
		$dbc = connectToDB();
		$query = "SELECT *
					FROM COURSE
					WHERE SEMESTER=:semester
					ORDER BY CLASS";
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
				<td align=\"center\" colspan=\"2\">Comments or Special Request:</td>
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
				<td colspan=\"2\">Has your address changed?
					Yes<input type=\"radio\" name=\"changeAddr\" value=\"1\"> 
					No<input type=\"radio\" name=\"changeAddr\" value=\"0\" checked=\"checked\"> 
				</td>
				<td colspan=\"8\">				   
					If Yes, please enter your new address: <input type=\"text\" name=\"newAddr\" id=\"newAddr\" size=\"100\">
				</td>
				</tr>
			<tr>
				<td colspan=\"2\">Has your phone changed?
					Yes<input type=\"radio\" name=\"changePhone\" value=\"1\"> 
					No<input type=\"radio\" name=\"changePhone\" value=\"0\" checked=\"checked\"> 
				</td>
				<td colspan=\"8\">				   
					If Yes, please enter your new phone number: <input type=\"text\" name=\"newPhone\" id=\"newPhone\" size=\"100\">
				</td>
				</tr>
			<tr>
				<td colspan=\"10\" align=\"center\"><input type=\"submit\" name=\"submit\" id=\"submit\" value=\"submit\"></td>
				</tr>";
		echo "</table>
		<input type=\"hidden\" name=\"studentID\" value=\"".$studentID."\"".">".
		"<input type=\"hidden\" name=\"status\" value=\"".$status."\"".">".
		"<input type=\"hidden\" name=\"sid\" value=\"".$sid."\"".">".
		"<input type=\"hidden\" name=\"dob\" value=\"".$dob."\"".">".
		"</form>";
	}

	// Get Extra Course Information (CORE, AREA, DESCRIPTION)	
	function getCourseInfo($cid){
		global $program;
		$dbc = connectToDB();
		$query = "SELECT TITLE, CORE, AREA, PREREQUISITE
					FROM COURSE_CATALOG 
					WHERE CODE=:cid";
		$stmt = $dbc->prepare($query);
		$stmt->bindParam(":cid",$cid,PDO::PARAM_STR);
		$stmt->execute();
		closeDB($dbc);
		if($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			$row["CORE"] = (strpos($row["CORE"],$program)!==false)?'X':"";
			$row["AREA"] = (strpos($row["AREA"],$program)!==false)?'X':"";
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

	// Display student's course history
	function displayHistory(){
		global $curCourse, $takenCourse, $waivedCourse;
		$curCourse = displayCourse(DISPLAY_CUR);
		$takenCourse = displayCourse(DISPLAY_TAKEN);
		$waivedCourse =  displayCourse(DISPLAY_WAIVED);
	}

	// General Function to display Courses 
	function displayCourse($display){
		global $firstname, $lastname, $prerequisite;
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
					echo "<td align=\"center\">".$info["CORE"]."</td>";
					echo "<td align=\"center\">".$info["AREA"]."</td>";
					//echo "<td>".$info["PREREQUISITE"]."</td>";
					echo "<td>".$prerequisite[$row["CLASS"]]."</td>";
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
			$studentInfo ="<h2>Profile</h2>
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
					<th><strong>Advisor</strong></th>
					<th><strong>Thesis Advisor</strong></th>
					<th><strong>Address</strong></th>
				</tr>
				<tr align=\"left\">
					<td>".$result["ADVISOR"]."</td>
					<td>".$result["THESIS_ADVISOR"]."</td>
					<td>".$result["STREET"].", "
						.$result["CITY"].", ".$result["STATE"].", ".$result["ZIP"]."</td>
				</tr>
			</table>";	
		echo $studentInfo;
		return $studentInfo;
		}
		else{
			exit("Cannot find Student Information");
		} 
	}

	
?>
