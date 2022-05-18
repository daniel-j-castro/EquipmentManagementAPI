<?php
$db = new Database("webuser","ZJ3eR63jZpLo47ER","ase","localhost");
$dblink = $db->setupConnection();
if(isset($_REQUEST['sn'])){
	$sn = $_REQUEST['sn'];
	$regex = '(^SN-[0-9a-z]{32}$)';
	$regex2 = '(^[0-9a-z]{32}$)';
	if(preg_match($regex,$sn) || preg_match($regex2,$sn)){
		if(preg_match($regex2, $sn)){
			$sn = "SN-".$sn;
		}
		if($arr = $db->readDev($sn)){
			header('Content-Type: application/json');
			header('HTTP/1.1 200 OK');
			$output['Status']="Success";
			$output['MSG']="Successfully read device $sn.";
			$output['Serial Number'] = $arr['Serial Number'];
			$output['Manufacturer'] = $arr['Manufacturer'];
			$output['Device Type'] = $arr['Device Type'];
			$output['Active?'] = $arr['Active?'];
			$responseData = json_encode($output);
			echo $responseData;
			die();
		}
		else{
			header('Content-Type: application/json');
			header('HTTP/1.1 200 OK');
			$output['Status']="Not Found";
			$output['MSG']="Could not find device with Serial Number: $sn in database!";
			$responseData = json_encode($output);
			echo $responseData;
			die();
		}
	}
	else{
		header("Content-Type: application/json");
		header('HTTP/1.1 200 OK');
		$output['Status']='Invalid Data';
		$output['MSG']="Invlid Serial Number entered. Please enter SN-(32 lowercase letters or numbers) or just 32 lowercase letters or numbers.";
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}
}
else{
	header("Content-Type: application/json");
	header('HTTP/1.1 200 OK');
	$output['Status']="Invalid Data";
	$output['MSG']="No Serial Number provided. Endpoint Example: ?ReadDevice&sn={sn}";
	$responseData=json_encode($output);
	echo $responseData;
	die();	
}

?>
