<?php
	ob_start();
	// Check session status
	if(!isset($_SESSION)){
		session_start();
	}		

	if(!isset($_SESSION["firstname"]) ||
		!isset($_SESSION["lastname"]) ||
		!isset($_SESSION["comments"]) ||
		!isset($_SESSION["selectedCourse"])){
		exit("You don't have permission to access this page.");
	}
	
	echo "First name: ". $_SESSION["firstname"]."<br>";	
	echo "Last name: ". $_SESSION["lastname"]."<br>";	
	echo "Comments: ". $_SESSION["comments"]."<br>";	

	ob_flush();
?>
