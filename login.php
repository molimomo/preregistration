<?php
	require_once 'include/DBManagement_PDO.php';
	require_once 'include/utilPreregistration.php';
	ob_start();
	$errSID = $errDate = $errMsg = "";

	if($_SERVER["REQUEST_METHOD"]=="POST"){
		// Extract user's input
		$sid = santitizeString($_POST['sid']);
		$dob = $_POST["month"]."/".$_POST["day"]."/".$_POST["year"];
		
		// Check Student ID	
		if(empty($sid)){
			$errSID = "Please input your SID!";
		}
		else if(strtoupper($sid[0]) != 'S'			// check the first character
			|| (strlen($sid)!=5)					// check the length of sid
			|| (!is_numeric(substr($sid, 1, 4)))){	// check the remain character
			$errSID = "Wrong Student ID Format!";
		}

		// Check Date of Birth
		if(!checkdate($_POST["month"], $_POST["day"],$_POST["year"])){
			$errDate = "Invalid Date!<br>";
		}	

		// Check the existence of user
		if(!isValid($sid, $dob)){
			$errMsg = "Cannot find this record! Please check again!";
		}

		// Submit user's information to handling page
		if(empty($errSID) && empty($errDate) && empty($errMsg)){
			session_start();
			$_SESSION["sid"] = $sid;
			header("Location: studentInfo.php");
		}
	}
	ob_flush();
?>

<!DOCTYPE html>
<link href='include/css/style.css' rel='stylesheet' type='text/css'>
<html>
	<head>
		<meta charset="UTF8">
		<title>KSI Login System</title>
	</head>
	<body>
		<h1 align="center">Knowledge Systems Institute</h1>
		<h2 align="center">Pre-registration Login</h2>
		<p align="center">
		<ol>
			<li>Verify your login with student ID and Date of birth.</li>
			<li>Select course you would like to take in next semester.</li>
			<li>Submit your pre-registration.</li>
			<li>Note that you cannot submit twice, if you have any change request, please contact office.</li>
		</ol>
		</p>
		<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>">
			<table align="center">
				<tr>
					<td>Student ID:</td>
					<td>
						<input type="text" name="sid" size="15" required="required">
						<span class="error"><?php echo $errSID?></span>
					</td>
				</tr>
				<tr>
					<td>Date of Birth:</td>
					<td>
						<?php monthMenu();dayMenu();yearMenu(); ?>
						<span class="error"><?php echo $errDate?></span>
					</td>
				</tr>
			</table>
			<p align="center"><input type="submit" name="submit" value="Login"></p>
			<p align="center" class="error"><?php echo $errMsg?></p>
		</form>
	</body>
</html>
