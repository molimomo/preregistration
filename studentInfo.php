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
	<div>
		<h1>Pre-register for Courses</h1>
		<?php displayStudentInfo(); ?>
	</div>

	<div>
		<h2>Registration</h2>
	</div>

	<div>
		<h2>History</h2>
		<?php displayCurrentCourse(); ?>
		<?php displayTakenCourse(); ?>	
		<?php displayWaivedCourse(); ?>	
	</div>
</body>
</html>
