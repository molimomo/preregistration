<?php
	require_once 'include/DBManagement_PDO.php';
	ob_start();
	$errSID = $errUser = $errMsg = ""; 
	$dbc = connectToDB();
	//listAllContents("STUDENT");	
	/*$query = "SELECT TOP 10 * FROM STUDENT";
	$stmt = $dbc->prepare($query);
	$stmt->execute();
	while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
		//print_r(explode("/",$row["BIRTH"]));
		echo $row["BIRTH"]."<br>";
		$test = explode(" ",$row["BIRTH"]);
		echo $test[0]."<br>";	
		echo $test[1]."<br>";	
		echo $test[2]."<br>";

		if($test[0]=="Apr"){
			echo "Char"."<br>";
		}
		if($test[0]==4){
			echo "Num"."<br>";
		}

	
	}*/
	closeDB($dbc);	
	ob_flush();
?>

<?php
	function yearMenu(){
		$beginYear = 1950;
		$endYear = 2010;
		echo "<select name=\"year\">";
		for($i=$beginYear; $i<=$endYear; $i++){
			echo "<option value=\"".$i."\">".$i."</option>";
		}	
		echo "</select>";
	}

	function monthMenu(){
		$months = array(
			1=>"Jan",
			2=>"Feb",
			3=>"Mar",
			4=>"Apr",
			5=>"May",
			6=>"Jun",
			7=>"Jul",
			8=>"Aug",
			9=>"Sep",
			10=>"Oct",
			11=>"Nov",
			12=>"Dec"
			);
		echo "<select name=\"month\">";
		for($i=1; $i<=12; $i++){
			echo "<option value=\"".$months[i]."\">".$months[$i]."</option>";
		}	
		echo "</select>";
	}

	function dayMenu(){
		echo "<select name=\"day\">";
		for($i=1; $i<=31; $i++){
			echo "<option value=\"".$i."\">".$i."</option>";
		}	
		echo "</select>";
	}
?>

<!DOCTYPE html>
<link href='css/style.css' rel='stylesheet' type='text/css'>
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
			<li>Selec course you would like to take in next semester.</li>
			<li>Submit your pre-registration.</li>
			<li>Note that you cannot submit twice, i you have any change request, please contact office.</li>
		</ol>
		</p>
		<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>">
			<table align="center">
				<tr>
					<td>Student ID:</td>
					<td><input type="text" name="sid" size="15" required="required"></td>
					<td><span class="error"><?php echo $errSID?></span></td>
				</tr>
				<tr>
					<td>Date of Birth:</td>
					<td><?php monthMenu();dayMenu();yearMenu(); ?></td>
				</tr>
			</table>
			<p align="center"><input type="submit" name="submit" value="Log-In"></p>
			<p class="error"><?php echo $errMsg ?></p>
		</form>
	</body>
</html>
