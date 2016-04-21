<?php
	// Include MySQL login information
	require_once 'include/loginMySQL.php';

	// Connect to MySQL
	function connectToDB(){
		$db_connection = mysqli_connect(hostnameDB, usernameDB, passwordDB, databaseDB);
		if($db_connection){
			mysqli_set_charset($db_connection, "utf8");
			return $db_connection;
		}
		else{
			die("Error: Cannot connect to MySQL database: ".mysqli_connect_errno().PHP_EOL);
		}
	}

	// Close database
	function closeDB($dbc){
		mysqli_close($dbc);
	}

	// Check duplicate entry
	function isDuplicate($dbc, $name, $email){
		$query = "SELECT * FROM contacts 
				WHERE email='".$email."'";
		$result = $dbc->query($query);
		if($result){
			return $result->fetch_assoc();
		}	
		return false;
	}

	// Find cid according to email address
	function findCID($dbc, $email){
		$query = "SELECT * FROM contacts 
				WHERE email='".$email."'";
		$result = $dbc->query($query)->fetch_assoc();
		if($result){
			return $result['cid'];
		}
	}


	// Insert data to contacts table	
	function insertToContactsTable($dbc, $name, $email, $phone, $source){
		$date = date("Y-m-d");
		$query = "INSERT IGNORE INTO contacts(full_name, email, phone_number, refer_source, status, date) 
				VALUES('".$name."','".$email."','".$phone."','".$source."','prospective','".$date."');";
		$result = $dbc->query($query);
		if(!$result){
			die("Error: ".mysqli_error($dbc));
		}
	}

	// Insert data to messages table	
	function insertToMessagesTable($dbc, $comment, $type, $email){
		$date = date("Y-m-d");
		$cid = findCID($dbc, $email);
		$query = "INSERT IGNORE INTO messages(cid, message, type, status, date) 
				VALUES('".$cid."','".$comment."','".$type."','prospective','".$date."');";
		$result = $dbc->query($query);
		if(!$result){
			die("Error: ".mysqli_error($dbc));
		}
	}
	function testFun($dbc, $query){
		//$query = "SELECT * FROM contacts";
		$result = $dbc->query($query);
		if(!$result) die("Database access failed: " . $dbc->error);
	
		//$rows = $result->num_rows;
		//echo $rows;
		return $result;
		/*while($row = $result->fetch_assoc()){
			echo $row["email"]."\n";
		}*/
	}

	// Select data from contacts table
	function selectFromContactsTable($dbc, $query){
		$result = $dbc->query($query);
		if(!$result) die("Database access failed: " . $dbc->error);
		return $result;
	}


	// Select data from messages table  
	function selectFromMessagesTable(){
	} 
	
	//Database connection for "Select" statements
        function callDatabaseForSelect($query){
                $return = array();
                $db_con = connectToDB();

                $result = $db_con->query($query);

                if(!$result)
                {
                        die('Error: ' . mysqli_error($db_con));
                }
                while($row = $result->fetch_assoc())
                {
			echo $result["email"];
                        array_push($return, $row);
                }

                closeDB($db_con);
		var_dump($return);
                return $return;
        }

?>
