<?php
	ob_start();
	require_once 'include/DBManagement_PDO.php';
	require_once 'include/utilPreregistration.php';
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
	$curCourse = array();		// Array to store student's current courses
    $takenCourse = array();		// Array to store student's taken courses
    $waivedCourse = array();	// Array to store student's waived courses
	ob_flush();
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Knowledge Systems Institute :: Graduate School of Computer Science :: Online Pre-Registration</title>
	</head>
<body>
	<p>
		<h1>Pre-register for Courses</h1>
		<?php displayStudentInfo(); ?>
	</p>
	<p>
		<h2>History</h2>
		<?php displayHistory()?>
	</p>
	<p>
		<h2>Records</h2>
		<?php displayRecords();?>
	</p>
	<p>
		<h2>Pre-Registration</h2>
		<?php displayProspectiveCourse();?>
	</p>



</body>
</html>
