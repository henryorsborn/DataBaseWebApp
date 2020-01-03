<?php include('functions.php');
	$input = $_POST["input"];
	
	$cred = array_map('trim',getCredentials());
	$conn = new mysqli($cred[0], $cred[1], $cred[2], $cred[3]);
	if(!$conn){
		die("Failed to connect to mysql".mysqli_connect_error());
	}
	$output = array("response"=>$input);
	switch($input["action"]){
		
		case "show_tables":
			$result=mysqli_query($conn, "show tables");
			if ($conn->error) {
				$output["data"] = $conn->error;
			} else {
				$current_table = array();
				while($row = $result->fetch_assoc()){
					$little_table = array();
					
					$little_table["name"] = $row[array_keys($row)[0]];
					$select_query = "select * from ".$little_table["name"];
					$describe_query = "describe ".$little_table["name"];
					$description_result = mysqli_query($conn, $describe_query);
					if(!$conn->error){
						$headers = array();
						while($description_row = $description_result->fetch_assoc()){
							array_push($headers, $description_row);
						}
						$little_table["headers"] = $headers;
					} else {
						$little_table["headers"] = $conn->error;
					}
					$secondary_result = mysqli_query($conn, $select_query);
					if(!$conn->error){
						$secondary_data = array();
						while($secondary_row = $secondary_result->fetch_assoc()){
							array_push($secondary_data, $secondary_row);
						}
						$little_table["data"] = $secondary_data;
					} else {
						$little_table["data"] = $conn->error;
					}
					array_push($current_table, $little_table);
				}
				$output["data"] = $current_table;
			}
			break;
	
		case "update":
			$name = $input["data"]["name"];
			$output["data"] = "Successfully Updated";
			foreach($input["data"]["data"] as $stuff){
				$keys = array_keys($stuff);
				$current_query = "update ".$name." set ";
				for($i=1;$i<sizeof($stuff);$i++){
					$current_query .= $keys[$i]." = '".$stuff[$keys[$i]]."', ";
				}
				$final_query = substr($current_query, 0, strlen($current_query)-2)." where ".$keys[0]." = ".$stuff[$keys[0]];
				mysqli_query($conn, $final_query);
				if($conn->error){
					$output["data"] = $conn->error;
				}
			}
			break;
			
	}
	echo htmlspecialchars(json_encode($output), ENT_NOQUOTES);
?>



