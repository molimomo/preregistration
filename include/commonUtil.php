<?php
	function santitizeString($var){
		$var = trim($var);
		$var = stripslashes($var);
		$var = strip_tags($var);
		$var = htmlentities($var);
		return $var;
	}
?>
