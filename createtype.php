<?php
$db = new Database("webuser","ZJ3eR63jZpLo47ER","ase","localhost");
$dblink = $db->setupConnection();
if(isset($_REQUEST['type']) && !empty($_REQUEST['type'])){
	$typestr = strtolower($_REQUEST['type']);
	$type = addslashes($typestr);	
	if(!($db->insertDT($type))){
		if(!($db->grabErrno() == 1062)){
			header('Content-Type: application/json');
			header('HTTP/1.1 500 Internal Server Error');
			$output['Status']= "Error";
			$output['MSG'] = "Internal Server Error!";
			$responseData=json_encode($output);
			echo $responseData;
			die();
		}
		else{
			header('Content-Type: application/json');
			header('HTTP/1.1 200 OK');
			$output['Status']= "Error";
			$output['MSG'] = "Unable to create Device Type due to duplicate key $typestr!";
			$responseData=json_encode($output);
			echo $responseData;
			die();
		}
	}
	else{
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output['Status'] = "Success";
		$output['MSG'] = "Device Type $typestr successfully created!";
		$output['Device Type'] = $typestr;
		$responseData = json_encode($output);
		echo $responseData;
		die();
	}
}
else{
	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output['Status']="Invalid Data";
	$output['MSG']="No type provided. Endpoint Example: CreateDeviceType&type={type}";
	$responseData=json_encode($output);
	echo $responseData;
	die();
}
?>
