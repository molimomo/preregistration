<?php
	// Login information for SMTP account
	define('mailHost','smtp.gmail.com');		// Mail host (SMTP server)
	define('mailSMTPSecure','ssl');			// Mail host SMTP Secure, the value could be 'ssl' or 'tls'
	define('mailPort',465);				// TCP port to connect 
	define('mailDebugLevel',0);			// Debug Level: 0 = off, 1 = client messages, 2 = client & server messages  
	define('mailUsername','');		// ContactUs mail SMTP username
	define('mailPassword',''); 	// ContactUs mail SMTP password
	define('mailRecipient',''); 	// The email address (office@ksi.edu) that receiving mail from students
?>
