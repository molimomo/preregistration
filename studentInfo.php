<?php
	ob_start();
	require_once 'include/utilStudentInfo.php';
	// Need the session
	if(!isset($_SESSION)){
		session_start();
	}	

	// Check session status	
	if(!isset($_SESSION['sid'])
		|| (!isset($_SESSION['dob']))){
		exit("You don't have the permission to access this page.");
	}

	// Global varaibles for further usages
	$sid = $_SESSION['sid'];
	$dob = $_SESSION['dob'];
	$firstname = $lastname = $program = $status = $studentID = "";	
	
	// Array to store student's current courses
	$curCourse = array();		
	
	// Array to store student's taken courses
    $takenCourse = array();		
	
	// Array to store student's waived courses
    $waivedCourse = array();

	// Array to store course and its prerequitsite
	$prerequisiteInfo = array();

	// Error messages for PHP validation
	$errMsg="";	

	// Form Submission
	if($_SERVER["REQUEST_METHOD"]=="POST"){
		echo "submit!<br>";
		//Validation for selected courses
			// 1. Empty selection
		if(!isset($_POST["selectedCourse"])){
			$errMsg.="You have to select at least 1 course!<br>";
		}
		else{
			// 2. Conflict Schedule
			$conflict = checkConflict($_POST["selectedCourse"]);
			if(!empty($conflict)){
				$errMsg.="You have conflict schedule! ".$conflict."<br>";
			}
		}

		// Submit Form
		if(empty($errMsg)){
			session_start();
			$_SESSION["selectedCourse"] = $_POST["selectedCourse"];
			$_SESSION["comments"] = santitizeString($_POST["comments"]);
			$_SESSION["status"] = $_POST["status"];
			$_SESSION["changeMajor"] = ($_POST["changeMajor"]==1)?"YES":"NO";
			$_SESSION["changeAddr"] = ($_POST["changeAddr"]==1)?"YES":"NO";
			$_SESSION["changePhone"] = ($_POST["changePhone"]==1)?"YES":"NO";
			$_SESSION["dob"] = $_POST["dob"];
			$_SESSION["newAddr"] = santitizeString($_POST["newAddr"]);
			$_SESSION["newPhone"] = santitizeString($_POST["newPhone"]);
			$_SESSION["studentID"] = $_POST["studentID"];	// PK in STUDENT table
			$_SESSION["sid"] = $_POST["sid"];				// Format: SXXXX
			header("Location: processForm.php");
		}
	}
	ob_flush();
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Knowledge Systems Institute :: Graduate School of Computer Science :: Online Pre-Registration</title>
		<link rel="stylesheet" type="text/css" href="include/css/style.css">
	</head>
<body>
	<p>
		<h1>Pre-register for Courses</h1>
		<?php displayStudentInfo($sid, $dob); ?>
	</p>
	<p>
		<h2>History</h2>
		<?php displayHistory();?>
	</p>
	<p>
		<h2>Pre-Registration</h2>
		<p> * If you have any question or special request about your preregistration, please leave your comment in the below form.</p>
		<span class="error"><?php echo $errMsg?></span>
		<?php displayPreregistrationForm();?>
	</p>
</body>
</html>
