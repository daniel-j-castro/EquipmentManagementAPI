<?php
$db = new Database(<DBINFO>);
$dblink = $db->setupConnection();
if(isset($_REQUEST['manu']) && !empty($_REQUEST['manu'])){
	$manustr = $_REQUEST['manu'];
	$manu = addslashes($manustr);	
	if(!($db->insertManu($manu))){
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
			$output['MSG'] = "Unable to create Manufacturer due to duplicate key $manustr!";
			$responseData=json_encode($output);
			echo $responseData;
			die();
		}
	}
	else{
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output['Status'] = "Success";
		$output['MSG'] = "Manufacturer $manustr successfully created!";
		$output['Manufacturer'] = $manustr;
		$responseData = json_encode($output);
		echo $responseData;
		die();
	}
}
else{
	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output['Status']="Invalid Data";
	$output['MSG']="No manu provided. Endpoint Example: CreateManufactuer&manu={manu}";
	$responseData=json_encode($output);
	echo $responseData;
	die();
}
?>
