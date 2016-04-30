<?php
	/*function sendEmailToOffice(){

	}

	function saveToDB(){

	}

	*/
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
		echo "<h2>The information you provided in Pre-Registration:</h2><br>";
		displayStudentInfo($sid, $dob);
		echo "<h2>Selected Courses and Estimated Fee</h2>";
		echo "<table width=\"100%\" align=\"center\" border=\"1\">";
		echo "<tr>
				<th colspan=\"5\">Item</th>
				</tr>";
		echo "<tr>
				<th>Class</th>
				<th>Title</th>
				<th>Day</th>
				<th>Time</th>
				<th>Fee</th>
				</tr>";
		for($i=0;$i<$courseCnt;$i++){
			// Split selected string to extract course information
			// Class/Day/Time/Tuition/Title
			$tmp = explode("/",$selected[$i]);
			$courseFee = getCourseFee($tmp[0], $tmp[3]);
			$estimatedFee+=$courseFee;
			if(isOnline($tmp[0])){
				$onlineCnt++;
			}
			echo "<tr>
					<td align=\"center\">".$tmp[0]."</td><td>".$tmp[4]."</td><td align=\"center\">".$tmp[1]."</td><td align=\"center\">".$tmp[2]."</td>";
			echo "<td align=\"center\">". $courseFee ."</td></tr>";
		}
		echo "<tr><td align=\"right\" colspan=\"4\">Material Fee: ".$courseCnt." courses * $".MATERIAL_FEE."</td>
				<td align=\"center\">". $materialFee ."</td></tr>";
		$labFee = $onlineCnt * ONLINE_LAB_FEE + ONSITE_LAB_FEE;
		$totalFee = $estimatedFee + $materialFee + $labFee;
		echo "<tr>
				<td align=\"right\" colspan=\"4\">Lab Fee: ".($courseCnt-$onlineCnt).
					" Onsite courses - $".ONSITE_LAB_FEE." + ".
					$onlineCnt. " Online courses * $".ONLINE_LAB_FEE."</td><td align=\"center\">". $labFee ."</td></tr>";
		echo "<tr><td align=\"right\" colspan=\"4\"><strong>Total:</strong></td>
					<td align=\"center\"><strong>$totalFee</strong> </td></tr>";
		echo "</table>";
		//sendToOffice();
		//saveToDB();
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

	if(!isset($_SESSION["firstname"]) ||
		!isset($_SESSION["lastname"]) ||
		!isset($_SESSION["comments"]) ||
		!isset($_SESSION["studentID"]) ||
		!isset($_SESSION["status"]) ||
		!isset($_SESSION["selectedCourse"])){
		exit("You don't have permission to access this page.");
	}
	
	$selected = $_SESSION["selectedCourse"];
	ob_flush();
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Pre-Registration Completed Page </title>
	</head>
	<body>
		<h1 align="center">Pre-registration Form Successfully Submitted </h1>
		<p>
			<?php displayResult(); ?>
		</p>
	</body>
</html>






