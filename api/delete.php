<?php
$db = new Database(<DBINFO>);
$dblink = $db->setupConnection();
if(isset($_REQUEST['sn'])){
	$sn = $_REQUEST['sn'];
	$regex = '(^SN-[0-9a-z]{32}$)';	
	$regex2 = '(^[0-9a-z]{32}$)';
	if(preg_match($regex, $sn) || preg_match($regex2, $sn)){
		if (preg_match($regex2,$sn)){
			$sn = "SN-".$sn;
		}
		if(!($check = $db->deleteDevice($sn))){
			if($check == 0){
				header('Content-Type: application/json');
				header('HTTP/1.1 500 Internal Server Error');
				$output['Status']= "Error";
				$output['MSG'] = "Internal Server Error!";
				$responseData=json_encode($output);
				echo $responseData;
				die();
			}
		}
		else{
			if($check == 1){
				header('Content-Type: application/json');
				header('HTTP/1.1 200 OK');
				$output['Status'] = "Success";
				$output['MSG'] = "Device with Serial Number: $sn successfully deleted!";
				$responseData = json_encode($output);
				echo $responseData;
				die();
			}
			else{
				header('Content-Type: application/json');
				header('HTTP/1.1 200 OK');
				$output['Status'] = "Device Not Found";
				$output['MSG'] = "Device with Serial Number: $sn not in database!";
				$responseData = json_encode($output);
				echo $responseData;
				die();
			}
		}
	}
	else{
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output['Status']="Invalid Data";
		$output['MSG']="Invalid serial number entered. SN-(32 lowercase letters or numbers) or (32 lowercase letters or numbers)";
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}
}
else{
	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output['Status']="Invalid Data";
	$output['MSG']="No serial number provided. Endpoint Example: DeleteDevice&sn={sn}";
	$responseData=json_encode($output);
	echo $responseData;
	die();
}
?>
