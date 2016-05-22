<?php
	ob_start();
	require_once "include/utilProcessForm.php";

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
