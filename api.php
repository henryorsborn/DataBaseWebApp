<?php include("functions.php");
	$input = $_POST["input"];
	
	//$json = file_get_contents("php://input");
	
	//$input = json_decode($json);
	
	$cred = array_map('trim',getCredentials());
	$conn = new mysqli($cred[0], $cred[1], $cred[2], $cred[3]);
	if(!$conn){
		die("Failed to connect to mysql".mysqli_connect_error());
	}
	$output = array("response"=>$input);
	$outputrefined = array();
	$actions = $input["action"];
	$x = 0;
	foreach($actions as $action){
		
		switch($action){
			
			case "show_tables":
				$result=mysqli_query($conn, "show tables");
				if ($conn->error) {
					$outputrefined[$x]["data"] = $conn->error;
				} else {
					$current_table = array();
					while($row = $result->fetch_assoc()){
						$little_table = array();
						
						$little_table["name"] = $row[array_keys($row)[0]];
						
						/*
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
						*/
						array_push($current_table, $little_table);
					}
					$outputrefined["data"][$x] = $current_table;
				}
				break;
				
			case "select_from":
				$name = $input["data"][$x]["name"];
				$sql = "select * from ".$name;
				
				$result = mysqli_query($conn, $sql);
				$output_data = array();
				$i = 0;
				while($row = $result->fetch_assoc()){
					$output_data[$i] = $row;
					$i++;
				}
				if($conn->error){
					$outputrefined["data"][$x] = $conn->error;
				} else {
				
					$outputrefined["data"][$x] = $output_data;
				}
				break;
				
			case "describe":
				$name = $input["data"][$x]["name"];
				$sql = "describe ".$name;
				$result = mysqli_query($conn, $sql);
				$output_data = array();
				$i = 0;
				while($row = $result->fetch_assoc()){
					$output_data[$i] = $row;
					$i++;
				}
				if($conn->error){
					$outputrefined["data"][$x] = $conn->error;
				} else {
					$outputrefined["data"][$x] = $output_data;
				}
				break;
		
			case "update":
				$name = $input["data"][$x]["name"];
				$outputrefined["data"][$x] = "Successfully Updated";
				foreach($input["data"][$x]["data"] as $stuff){
					$keys = array_keys($stuff);
					$current_query = "update ".$name." set ";
					for($i=1;$i<sizeof($stuff);$i++){
						$current_query .= $keys[$i]." = '".$stuff[$keys[$i]]."', ";
					}
					$final_query = substr($current_query, 0, strlen($current_query)-2)." where ".$keys[0]." = ".$stuff[$keys[0]];
					mysqli_query($conn, $final_query);
					if($conn->error){
						$outputrefined["data"][$x] = $conn->error;
					}
				}
				break;

			
			case "delete_row":
				$tableName = $input["data"][$x]["name"];
				$rowId = $input["data"][$x]["id"];
				$sqlStarter = "describe ".$tableName;
				$result = mysqli_query($conn, $sqlStarter);
				while($row = $result->fetch_assoc()){
					if($row["Key"] == "PRI"){
						$sql = "delete from ".$tableName." where ".$row["Field"]." = ".$rowId;
						mysqli_query($conn, $sql);
						if($conn->error){
							$outputrefined["data"][$x] = $conn->error;
						} else {
							$outputrefined["data"][$x] = "Successfully Deleted";
						}
						break;
					}
				}
				break;
			
			default:
				$outputrefined[0] = "Error: Action '".$input["action"]."' not found";
				break;

		}
		$x++;
	}
	echo htmlspecialchars(json_encode($outputrefined), ENT_NOQUOTES);
?>



