<?php
	/* DBManagement_PDO.php
	*	@Author: Mao-Lin Li
	*	@Last modified Date: 04/02/2016
	*	@Description:
	*		This file provide general database operations with PHP Data Object (PDO)
	*		, which including
	*		1. Connect to database (connectToDB)
	*		2. Close database connection (closeDB)
	*		3. List all contents in a table (listAllContents)
	*		4. Check the existence of a value in a table (isExist)
	*		5. Get all fields' names in a table (getTableFields)
	*/

	// Include MSSQL login information
	require_once "loginMSSQL.php";	
	//require_once 'loginMSSQL.php';	
	// Connect to MSSQL DB
	function connectToDB(){
		$dsn = "dblib:host=".hostnameDB.";dbname=".databaseDB;
		try{
			$dbc = new PDO($dsn, usernameDB, passwordDB);
			$dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			return $dbc;
		}
		catch(PDOException $ex){
			echo "Connection failed: ".$ex->getMessage();
		}
	}

	// Close database
	function closeDB($dbc){
		$dbc = null;
	}
	
	// Get all fields' name in a table
	function getTableFields($table){
		try{
			$dbc = connectToDB();
			$query = "SELECT TOP 1 * FROM ".$table;
			$results = $dbc->query($query);
			for($i=0;$i<$results->columnCount();$i++){
				$col = $results->getColumnMeta($i);
				$columns[] = $col["name"];
			}
			closeDB($dbc);
			return $columns;
		}
		catch(PDOException $ex){
			echo "Get Table Fields failed: ".$ex->getMessage();
		}
	}

	// List all contents in a table.	
	function listAllContents($table){
		try{
			$dbc = connectToDB();
			$columns = getTableFields($table);
				
			echo "<table border=1>";
			echo "<tr>";
			for($i=0;$i<count($columns);$i++){
				echo "<th>".$columns[$i]."</th>";
			}
			echo "</tr>";

			$query = "SELECT * FROM ".$table;
			$stmt = $dbc->prepare($query);
			$stmt->execute();
				
			while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
				echo "<tr>";
				for($i=0;$i<count($columns);$i++){
					echo "<td>".$row[$columns[$i]]."</td>";
				}
				echo "</tr>";
			}
			echo "</table>";
			closeDB($dbc);
		}
		catch(PDOException $ex){
			echo "List All Contents failed: ".$ex->getMessage();
		}
	}

	// Check the existence of given value in a table
	function isExist($table, $field, $value){
		try{
			$dbc = connectToDB();
			$query = "SELECT ".$field." FROM ".$table." WHERE ".$field."=:value";
			$stmt = $dbc->prepare($query);
			$stmt->bindParam(':value',$value,PDO::PARAM_STR);
			$stmt->execute();
			closeDB($dbc);
			return $stmt->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $ex){
			echo "Check Existence failed: ".$ex->getMessage();
		}
	}
?>
