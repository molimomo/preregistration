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
	
		* (constant in program)
		- DISPLAY_CUR
		- DISPLAY_TAKEN
		- DISPLAY_WAIVED
	*/

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
	$status[0] = "Part-Time";
	$status[1] = "";
	$status[2] = "Full-Time";
	$status[3] = "J-1";
	$status[4] = "F-1";
	$status[5] = "F-2";
	$status[6] = "H-1";
	$status[7] = "H-4";
	$status[8] = "G-1";
	$status[9] = "M-1";
	$status[10] = "B-1";
	$status[11] = "B-2";
	$status[12] = "WITH DRAW";
	$status[13] = "CPT";
	$status[14] = "OPT";
	$status[15] = "GREEN CARD";
	$status[16] = "NOT IN US";
	$status[17] = "OUT OF STATUS";
	$status[18] = "OTHERS";

	// Waived Examine
	$waivedType[0] = "TRASFERRED_WAIVER";	
	$waivedType[1] = "KSI_WAIVER";	
	
	// Degree
	define("NON_DEGREE",0);
	define("DEGREE",-1);

	// Display Options
	define("DISPLAY_CUR", 0);
	define("DISPLAY_TAKEN", 1);
	define("DISPLAY_WAIVED", 2);
?>
