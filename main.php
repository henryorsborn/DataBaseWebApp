
<?php ini_set('display_errors', 1); include('functions.php') ?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewort" content="width=device-width, initial scale=1">
		<title>Database Manager</title>
		<!--
		<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrap/3.3.5/css/bootstrap.min.css">
		-->
	</head>
	<body>
		<?php
			$tables = array();
			$cred = array_map('trim',getCredentials());
			$conn = new mysqli($cred[0], $cred[1], $cred[2], $cred[3]);
			if(!$conn){
				die("Failed to connect to mysql".mysqli_connect_error());
			}
			$table_get_sql="show tables";
			if(empty($result=mysqli_query($conn, $table_get_sql))){
				echo "Something went wrong\n".$conn->error;
			} else {
				echo "<h1>These are the tables we found</h1><br>";
			}
			
			while($row = $result->fetch_assoc()){
				$current_table = array();
				$current_table["name"] = $row["Tables_in_database_git_project"];
				array_push($tables, $current_table);
				
			}
			foreach($tables as $item){
				echo "<h2>".$item["name"]."</h2>";
				$select_query = "select * from ".$item["name"];
				if(empty($result=mysqli_query($conn, $select_query))){
					echo "No rows found<br>\n";
				} else {
					echo "<table>"
					while($row = $result->fetch_assoc()){
						foreach(array_keys($row) as $key){
							echo $row[$key];	
						}
					}
				}	
			}
			$conn->close()
		?>


	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	</body>
</html>

