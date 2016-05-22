# Pre-registration in KSI
Pre-Register System in KSI.

# Usage:
This application lets students fill the information for pre-registration. After submitting data, the data will send to administrative office and store to database.

# How to use
	1. Setup MS SQL Server Database
		- Change the constants in /preregistration/include/loginMSSQL.php
			* hostnameDB - the url or ip address of database server
			* databaseDB - the name of database
			* usernameDB - the account in MS SQL server
			* passwordDB - the password in MS SQL server

	2. Setup PHPMailer
		- Change the constants in /preregistration/include/loginSMTP.php
			* mailUsername
			* mailPassword
			* mailRecipient 

	3. Go to http://XXX.XXX.XXX.XXX( your server ip)/preregistration/login.php

	4. User should type in their student ID and Date of Birth to login.

	5. If user login successfully, user should see their course history and pre-registration form.

	6. After user submitting their pre-registration form, they should see the result and estimated fee.

	7. Above results will be sent to KSI office by email and store into database.

# File Description: (d) - directory
	- login.php			// Student Login Page
	- studentInfo.php		// Student Information and Pre-registration Page
	- processForm.php		// Process pre-registration result with email and database
	- (d) include
		- commonUtil.php	// Common Helper Functions
		- DBManagement_PDO.php	// Database operations in PDO
		- loginMSSQL.php	// Login information for MS SQL Server
		- loginSMTP.php		// Login information for PHPMailer
		- utilLogin.php		// Helper functions for login.php
		- utilStudentInfo.php	// Helper functions for studentInfo.php
		- utilProcessForm.php	// Helper functions for precessForm.php
		- valueMapping.php	// Value representation for Pre_Registration Database
		- (d) PHPMailer		// PHPMailer Component
		- (d) js		// javascript helper functions
		- (d) css		// CSS files


 
