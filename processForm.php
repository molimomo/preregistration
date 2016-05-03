<?php
	function sendEmailToOffice($studentInfo, $selectedInfo){
		$mail = new PHPMailer;
		$comment = $_SESSION["comments"];
		$changeMajor = $_SESSION["changeMajor"];
		$mail->isSMTP();            // Set mailer to use SMTP
    		$mail->SMTPDebug = mailDebugLevel;
   	 	$mail->Host = mailHost;
    		$mail->SMTPAuth = true;         // Enable SMTP authentication
    		$mail->Username = mailUsername;
    		$mail->Password = mailPassword;
    		$mail->SMTPSecure = mailSMTPSecure;
    		$mail->Port = mailPort;
    		$mail->setFrom(mailUsername,"KSI Preregistration");
    		$mail->addAddress(mailRecipient);
    		$mail->isHTML(true);            // Set email format to HTML
    		$mail->Subject = 'New pre-registration form submitted';
    		$mailBody =	$studentInfo.$selectedInfo;
		$mail->Body    = $mailBody;
    		$mail->AltBody = $mailBody;
    		return ($mail->Send());	
	}
	

	// Save StudentID and CourseID into database
	function saveToDB($studentID, $courseIDs){
		$dbc = connectToDB();
		$date = date("m-d-Y");
		for($i=0;$i<count($courseIDs);$i++){
			$query = "INSERT INTO PRE_REGISTER (StudentID, CourseID, SubmitTime)
				VALUES(:studentID, :courseID, :date)";
			$stmt = $dbc->prepare($query);
			$stmt->bindParam(":studentID",$studentID,PDO::PARAM_STR);
			$stmt->bindParam(":courseID",$courseIDs[$i],PDO::PARAM_STR);
			$stmt->bindParam(":date",$date,PDO::PARAM_STR);
			$stmt->execute();
		}
		closeDB($dbc);
	}

	// Get tuition fee according to class and status.
	function getCourseFee($class, $fee){
		global $statusMap;
		if(strcmp("CIS600A",$class)!=0 
			&& strcmp("CIS600",$class)!=0){
			// check the status of student, international: 1.2, domestic: 1 
				$rate = (strcmp($statusMap[$_SESSION["status"]],"GREEN CARD") != 0)?INTERNATIONAL_RATE:DOMESTIC_RATE;
		}
		else{	
			// the rate of CIS600 and CIS600A are 1
			$rate = DOMESTIC_RATE; 
		}
		return $fee * $rate;
	}

	// Check whether the class is online or not
	function isOnline($class){
		return (substr($class, -1) == 'X');
	}
	
	// Display the result that student 
	function displayResult(){
		$selected = $_SESSION["selectedCourse"];
		$sid = $_SESSION["sid"];
		$dob = $_SESSION["dob"];
		$courseCnt = count($selected);
		$materialFee= $courseCnt * MATERIAL_FEE; 
		$estimatedFee = 0;
		$onlineCnt = 0;
		$courseIDs = array();
		$selectedInfo ="";
		echo "<h2>The information you provided in Pre-Registration:</h2><br>";
		$studentInfo = displayStudentInfo($sid, $dob);
		$selectedInfo ="<h2>Selected Courses and Estimated Fee</h2>";
		$selectedInfo.= "<table width=\"100%\" align=\"center\" border=\"1\">";
		$selectedInfo.= "<tr>
				<th colspan=\"5\">Item</th>
				</tr>";
		$selectedInfo.= "<tr>
				<th>Class</th>
				<th>Title</th>
				<th>Day</th>
				<th>Time</th>
				<th>Fee</th>
				</tr>";
		for($i=0;$i<$courseCnt;$i++){
			// Split selected string to extract course information
			// Class/Day/Time/Tuition/Title/CourseID
			$tmp = explode("/",$selected[$i]);
			$courseFee = getCourseFee($tmp[0], $tmp[3]);
			array_push($courseIDs, $tmp[5]);
			$estimatedFee+=$courseFee;
			if(isOnline($tmp[0])){
				$onlineCnt++;
			}
			$selectedInfo.= "<tr>
					<td align=\"center\">".$tmp[0]."</td><td>".$tmp[4]."</td><td align=\"center\">".$tmp[1]."</td><td align=\"center\">".$tmp[2]."</td>";
			$selectedInfo.= "<td align=\"center\">". $courseFee ."</td></tr>";
		}
		$selectedInfo.= "<tr><td align=\"right\" colspan=\"4\">Material Fee: ".$courseCnt." courses * $".MATERIAL_FEE."</td>
				<td align=\"center\">". $materialFee ."</td></tr>";
		$labFee = $onlineCnt * ONLINE_LAB_FEE + ONSITE_LAB_FEE;
		$totalFee = $estimatedFee + $materialFee + $labFee;
		$selectedInfo.= "<tr>
				<td align=\"right\" colspan=\"4\">Lab Fee: ".($courseCnt-$onlineCnt).
					" Onsite courses - $".ONSITE_LAB_FEE." + ".
					$onlineCnt. " Online courses * $".ONLINE_LAB_FEE."</td><td align=\"center\">". $labFee ."</td></tr>";
		$selectedInfo.= "<tr><td align=\"right\" colspan=\"4\"><strong>Total:</strong></td>
					<td align=\"center\"><strong>$totalFee</strong> </td></tr>";
		$selectedInfo.= "<tr><td align=\"center\"><strong>Comments:</strong></td>
					<td colspan=\"4\">".$_SESSION["comments"]."</td></tr>";
		$selectedInfo.= "<tr><td align=\"center\"><strong>Considering Changing Major?:</strong></td>
					<td colspan=\"4\"><strong>".$_SESSION["changeMajor"]."</strong></td></tr>";
		$selectedInfo.= "</table>";
		echo $selectedInfo;
		sendEmailToOffice($studentInfo, $selectedInfo);
		//saveToDB($_SESSION["studentID"], $courseIDs);
	}
?>
<?php
	ob_start();
	require_once 'include/DBManagement_PDO.php';
	require_once 'include/utilPreregistration.php';
	// Check session status
	if(!isset($_SESSION)){
		session_start();
	}		

	if(!isset($_SESSION["studentID"]) ||
		!isset($_SESSION["status"]) ||
		!isset($_SESSION["changeMajor"]) ||
		!isset($_SESSION["selectedCourse"])){
		exit("You don't have permission to access this page.");
	}
	ob_flush();
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Pre-Registration Completed Page </title>
	</head>
	<body>
		<h1 align="center" style="color:red">Pre-registration Form Successfully Submitted </h1>
		<p>
			<?php displayResult(); ?>
		</p>
	</body>
</html>
