<?php
$db = new Database(<DBINFO>);
$dblink = $db->setupConnection();
if(isset($_REQUEST['manu']) || isset($_REQUEST['type'])){
	if(isset($_REQUEST['manu'])){
		if(!in_array($_REQUEST['manu'],$db->grabAllManu())){
			header("Content-Type: application/json");
			header("HTTP/1.1 200 OK");
			$output['Status']="Invalid Data";
			$output['MSG']='Error: Manufacturer - '.$_REQUEST['manu'].' not in database!';
			$responseData=json_encode($output);
			echo $responseData;
			die();
		}
		else{
			if(!$result = $dblink->query("select id from manufacturer where manufacturer='".$_REQUEST['manu']."'")){
				header("Content-Type: application/json");
				header("HTTP/1.1 500 Internal Server Error");
				$output['Status']="Internal Server Error";
				$output['MSG']="Internal Server Error";
				$responseData=json_encode($output);
				echo $responseData;
				die();
			}
			$res = $result->fetch_array(MYSQLI_ASSOC);
			$manu = $res['id'];
		}
	}
	else{
		$manu = 0;
	}
	if(isset($_REQUEST['type'])){
		if(!in_array(strtolower($_REQUEST['type']),$db->grabAllDT())){
			header("Content-Type: application/json");
			header("HTTP/1.1 200 OK");
			$output['Status']="Invalid Data";
			$output['MSG']='Error: Type - '.$_REQUEST['type'].' not in database!';
			$responseData=json_encode($output);
			echo $responseData;
			die();
		}
		else{
			if(!$result = $dblink->query("select id from device_type where d_type='".strtolower($_REQUEST['type'])."'")){
				header("Content-Type: application/json");
				header("HTTP/1.1 500 Internal Server Error");
				$output['Status']="Internal Server Error";
				$output['MSG']="Internal Server Error";
				$responseData=json_encode($output);
				echo $responseData;
				die();
			}
			$res = $result->fetch_array(MYSQLI_ASSOC);
			$type = $res['id'];
		}
	}
	else{
		$type = 0;
	}
	if(!isset($_REQUEST['pointer'])){
		$pointer = 0;	
	}
	else{
		$pointer = $_REQUEST['pointer'];
	}
	if(isset($_REQUEST['direction'])){
		if($pointer > 0){
			if(strtolower($_REQUEST['direction']) == "next"){
				$direction = ">";
			}
			else if(strtolower($_REQUEST['direction']) == "previous"){
				$direction = "<";
			}
			else{
				header("Content-Type: application/json");
				header("HTTP/1.1 200 OK");
				$output['Status']="Invalid Data";
				$output['MSG']='Error: Invalid Direction (next, previous)';
				$responseData=json_encode($output);
				echo $responseData;
				die();
			}
		}
		else{
			$direction = ">";
		}
	}
	else if($pointer == 0){
		$direction = ">";
	}
	else{
		header("Content-Type: application/json");
		header("HTTP/1.1 200 OK");
		$output['Status']="Invalid Data";
		$output['MSG']='Error: Cannot have no direction with pointer greater than 0';
		$responseData=json_encode($output);
		echo $responseData;
		die();

	}
	if($manu != 0){
		$condition = "manufacturer=$manu";
		if($type != 0){
			$condition = $condition." and type=$type";
		}
	}
	else{
		$condition = "type=$type";
	}
	if(($count = mysqli_fetch_array($dblink->query("select count(id) from equip where $condition"))[0]) === False){
			header("Content-Type: application/json");
			header("HTTP/1.1 500 Internal Server Error");
			$output['Status']="Internal Server Error";
			$output['MSG']="Internal Server Error";
			$responseData=json_encode($output);
			echo $responseData;
			die();
	}
	else if($count == 0){
		header("Content-Type: application/json");
		header("HTTP/1.1 200 OK");
		$output['Status']="Success";
		$output['MSG']="No records found with matching criteria.";
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}
	if(!$min = mysqli_fetch_array($dblink->query("select id from equip where $condition order by id asc limit 1"))[0]){
		header("Content-Type: application/json");
		header("HTTP/1.1 500 Internal Server Error");
		$output['Status']="Internal Server Error";
		$output['MSG']="Internal Server Error";
		$responseData=json_encode($output);
		echo $responseData;
		die();

	}
	if(!$max = mysqli_fetch_array($dblink->query("select id from equip where $condition order by id desc limit 1"))[0]){
		header("Content-Type: application/json");
		header("HTTP/1.1 500 Internal Server Error");
		$output['Status']="Internal Server Error";
		$output['MSG']="Internal Server Error";
		$responseData=json_encode($output);
		echo $responseData;
		die();

	}
	$sql = "select serial_num, id from equip where $condition and id $direction $pointer order by id limit 10";
	if($direction == '>'){
		if(!$result = $dblink->query($sql)){
			header("Content-Type: application/json");
			header("HTTP/1.1 500 Internal Server Error");
			$output['Status']="Internal Server Error";
			$output['MSG']="Internal Server Error";
			$responseData=json_encode($output);
			echo $responseData;
			die();
		}
		else{
			$c = 0;
			while($row = $result->fetch_array(MYSQLI_ASSOC)){
				$reversed[] = $row;
			}
			foreach($reversed as $res){
	//			echo $res['serial_num']."<br>";
				$c += 1;
				if($c == 1){
					$prePointer = $res['id'];
				}
				if($c == $result->num_rows){
					$newPointer = $res['id'];
				}
			}
			if($result->num_rows < 10 || $pointer == $max || $newPointer == $max){
				$next = "End of search";
			}
			else{
				if($manu != 0 && $type != 0){
					$next = "https://ec2-44-202-167-169.compute-1.amazonaws.com/api/?SearchDevices&manu=".$_REQUEST['manu']."&type=".$_REQUEST['type']."&pointer=".$newPointer."&direction=next";
	//				echo $next;
				}
				else if($manu != 0 && $type == 0){       	
					$next = "https://ec2-44-202-167-169.compute-1.amazonaws.com/api/?SearchDevices&manu=".$_REQUEST['manu']."&pointer=".$newPointer."&direction=next";
	//				echo $next;
				}
				else if($manu == 0 && $type != 0){       	
					$next = "https://ec2-44-202-167-169.compute-1.amazonaws.com/api/?SearchDevices&type=".$_REQUEST['type']."&pointer=".$newPointer."&direction=next";
	//				echo $next."<br>";
				}
			}
			if($pointer == 0 || $pointer == $min || $prePointer == $min){
				$previous = "Beginning of Search";
			}	
			else{
				if($manu != 0 && $type != 0){
					$previous = "https://ec2-44-202-167-169.compute-1.amazonaws.com/api/?SearchDevices&manu=".$_REQUEST['manu']."&type=".$_REQUEST['type']."&pointer=".$prePointer."&direction=previous";
	//				echo $previous;
				}
				else if($manu != 0 && $type == 0){       	
					$previous = "https://ec2-44-202-167-169.compute-1.amazonaws.com/api/?SearchDevices&manu=".$_REQUEST['manu']."&pointer=".$prePointer."&direction=previous";
	//				echo $previous;
				}
				else if($manu == 0 && $type != 0){       	
					$previous = "https://ec2-44-202-167-169.compute-1.amazonaws.com/api/?SearchDevices&type=".$_REQUEST['type']."&pointer=".$prePointer."&direction=previous";
	//				echo $previous."<br>";
				}
			}
		}	
	}
	$sql = "select serial_num, id from equip where $condition and id $direction $pointer order by id desc limit 10";
	if($direction == '<'){
		if(!$result = $dblink->query($sql)){
			header("Content-Type: application/json");
			header("HTTP/1.1 500 Internal Server Error");
			$output['Status']="Internal Server Error";
			$output['MSG']="Internal Server Error";
			$responseData=json_encode($output);
			echo $responseData;
			die();
		}
		else{
			$c = 0;
			while($row = $result->fetch_array(MYSQLI_ASSOC)){
				$toReverse[] = $row;
			}
			$reversed = array_reverse($toReverse);
			foreach($reversed as $res){
	//			echo $res['serial_num']."<br>";
				$c += 1;
				if($c == 1){
					$prePointer = $res['id'];
				}
				if($c == $result->num_rows){
					$newPointer = $res['id'];
				}
			}
			if($result->num_rows < 10 || $pointer == $max || $newPointer == $max){
				$next = "End of search";
			}
			else{
				if($manu != 0 && $type != 0){
					$next = "https://ec2-44-202-167-169.compute-1.amazonaws.com/api/?SearchDevices&manu=".$_REQUEST['manu']."&type=".$_REQUEST['type']."&pointer=".$newPointer."&direction=next";
	//				echo $next;
				}
				else if($manu != 0 && $type == 0){       	
					$next = "https://ec2-44-202-167-169.compute-1.amazonaws.com/api/?SearchDevices&manu=".$_REQUEST['manu']."&pointer=".$newPointer."&direction=next";
	//				echo $next;
				}
				else if($manu == 0 && $type != 0){       	
					$next = "https://ec2-44-202-167-169.compute-1.amazonaws.com/api/?SearchDevices&type=".$_REQUEST['type']."&pointer=".$newPointer."&direction=next";
	//				echo $next."<br>";
				}
			}
			if($pointer == 0 || $pointer == $min || $prePointer == $min){
				$previous = "Beginning of Search";
			}	
			else{
				if($manu != 0 && $type != 0){
					$previous = "https://ec2-44-202-167-169.compute-1.amazonaws.com/api/?SearchDevices&manu=".$_REQUEST['manu']."&type=".$_REQUEST['type']."&pointer=".$prePointer."&direction=previous";
	//				echo $previous;
				}
				else if($manu != 0 && $type == 0){       	
					$previous = "https://ec2-44-202-167-169.compute-1.amazonaws.com/api/?SearchDevices&manu=".$_REQUEST['manu']."&pointer=".$prePointer."&direction=previous";
	//				echo $previous;
				}
				else if($manu == 0 && $type != 0){       	
					$previous = "https://ec2-44-202-167-169.compute-1.amazonaws.com/api/?SearchDevices&type=".$_REQUEST['type']."&pointer=".$prePointer."&direction=previous";
	//				echo $previous."<br>";
				}
			}
		}	
	}
	//echo $sql;
	header("Content-Type: application/json");
	header("HTTP/1.1 200 OK");
	$output['Status']="Success";
	$output['MSG']="Successfully found devices that met criteria!";
	$output['Total']=$count;
	foreach($reversed as $res){
		$output[$res['serial_num']] = "https://ec2-44-202-167-169.compute-1.amazonaws.com/api/?ReadDevice&sn=".$res['serial_num'];
	}
	$output['Next']=$next;
	$output['Previous']=$previous;
	$responseData=json_encode($output);
	echo $responseData;
	die();
}
else{
	header("Content-Type: application/json");
	header("HTTP/1.1 200 OK");
	$output['Status']="Invalid Data";
	$output['MSG']="No Input entered. Endpoint Example: ?SearchDevices&manu={manu} or ?SearchDevices&type={type}";
	$responseData=json_encode($output);
	echo $responseData;
	die();
}
?>
