<?php function getCredentials(){

	//reads credentials from file
	//$credFile = fopen("logins/cred.txt", "r");
	$credentials = array("localhost", 
			     "horsborn",
		     	     "Fableah18461@",
			     "database_git_project");
	//fclose($credFile);
	return ($credentials);
}
?>
