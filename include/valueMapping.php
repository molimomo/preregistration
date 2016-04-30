<?php
	/*
		Ths is file records the mapping between 
		integer values and fileds in database.
		
		It includes:
		* (value - field mapping in database)
		- PRE_EDUCATION 	(in STUDENT table)
		- PRE_DEGREE 		(in STUDENT table)
		- STATUS			(in STUDENT table)
		- EXAMINE			(in COURSEWAIVED table)
		- DEGREE			(in COURSE table)
	
		* (constants in program)
		- DISPLAY_CUR
		- DISPLAY_TAKEN
		- DISPLAY_WAIVED
		- SEMESTER
		- DOMESTIC_RATE
		- INTERNATIONAL_RATE
		- ONLINE_LAB_FEE
		- ONSITE_LAB_FEE
		- MATERIAL_FEE
	*/

	// Tuition Constants
	define("DOMESTIC_RATE", 1);
	define("INTERNATIONAL_RATE", 1.2);
	define("ONLINE_LAB_FEE", 150.0);
	define("ONSITE_LAB_FEE", 50.0);
	define("MATERIAL_FEE", 5.0);

	// Semester
	define("SEMESTER","SPRING/2016");

	// Pre-Education
	$preEducation[0] = "Others";
	$preEducation[1] = "Master";
	$preEducation[2] = "Bachelor";

	// Pre-Degree
	$preDegree[0] = "Others";
	$preDegree[1] = "Bachelor of Science";
	$preDegree[2] = "Bachelor in Electronic Engineering";
	$preDegree[3] = "Master of Science";
	$preDegree[4] = "Master of Arts";
	$preDegree[5] = "Master of Business Administration";
	$preDegree[6] = "Master of Computer and Information Sciences";
	$preDegree[7] = "Master in Computer Science";
	$preDegree[8] = "Master of Engineering";
	$preDegree[9] = "Master of Fine Arts";
	$preDegree[10] = "Master of Industrial Technology and Operations";
	
	// Status
	$statusMap[0] = "Part-Time";
	$statusMap[1] = "";
	$statusMap[2] = "Full-Time";
	$statusMap[3] = "J-1";
	$statusMap[4] = "F-1";
	$statusMap[5] = "F-2";
	$statusMap[6] = "H-1";
	$statusMap[7] = "H-4";
	$statusMap[8] = "G-1";
	$statusMap[9] = "M-1";
	$statusMap[10] = "B-1";
	$statusMap[11] = "B-2";
	$statusMap[12] = "WITH DRAW";
	$statusMap[13] = "CPT";
	$statusMap[14] = "OPT";
	$statusMap[15] = "GREEN CARD";
	$statusMap[16] = "NOT IN US";
	$statusMap[17] = "OUT OF STATUS";
	$statusMap[18] = "OTHERS";

	// Waived Examine
	$waivedType[0] = "Transferred waiver";	
	$waivedType[1] = "KSI waiver examined";	
	
	// Degree
	define("NON_DEGREE",0);
	define("DEGREE",-1);

	// Display Options
	define("DISPLAY_CUR", 0);
	define("DISPLAY_TAKEN", 1);
	define("DISPLAY_WAIVED", 2);
?>
