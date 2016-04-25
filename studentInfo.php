<?php
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
	$sid = $_SESSION['sid'];
	$dob = $_SESSION['dob'];
	echo $sid."<br>";	
	echo $dob."<br>";	
	ob_flush();
?>
