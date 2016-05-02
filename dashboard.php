<?php
	ob_start();
	require_once "include/utilPreregistration.php";
	require_once "include/DBManagement_PDO.php";

	function listCurrentStatistic(){
		$dbc =  connectToDB();
		$query = "SELECT CourseID, COUNT(*) AS CourseCnt
					FROM PRE_REGISTER
					GROUP BY CourseID";
		$stmt = $dbc->prepare($query);
		$stmt->execute();	
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			$subQuery = "SELECT * FROM COURSE
						WHERE CourseID=:courseID";
			$subStmt =$dbc->prepare($subQuery);
			$subStmt->bindParam(":courseID",$row["CourseID"], PDO::PARAM_STR);
			$subStmt->execute();
			if($subRow = $subStmt->fetch(PDO::FETCH_ASSOC)){
				echo $subRow["CourseID"]." / ".$subRow["CLASS"]." / ".$subRow["TITLE"]." / ".$row["CourseCnt"];
			}
			//echo $row["CourseCnt"]."<br>";
		}	




	}
	
	function listPreRegistedStudents(){

	}
	
	ob_flush();
?>
<!DOCTYPE html>
<html>
	<head>
		<title>KSI Prg-Registration Dashboard</title>
	</head>
	<body>
		<h1>KSI Prg-Registration Dashboard</h1>
		<?php listCurrentStatistic(); ?>
	</body>		
</html>

