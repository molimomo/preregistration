<?php
	require_once 'include/DBManagement_PDO.php';
	require_once 'include/utilPreregistration.php';
	ob_start();
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
	$firstname = $lastname = $program = "";
	ob_flush();
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Knowledge Systems Institute :: Graduate School of Computer Science :: Online Pre-Registration</title>
	</head>
<body>
	<h1>Pre-register for Courses</h1>
	<?php
		displayStudentInfo();	
	?>
	<h2>History</h2>
	<?php
		displayTakenCourse();
	?>	
	<h2>Course Information</h2>
	<?php
		getCourseInfo("BA501");
	?>
</body>
</html>
