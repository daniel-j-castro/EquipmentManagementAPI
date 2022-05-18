<?php
$db = new Database(<DBINFO>);
$dblink = $db->setupConnection();
if(isset($_REQUEST['sn']) && isset($_REQUEST['manu']) && isset($_REQUEST['type'])){
	$sn = $_REQUEST['sn'];
	$type = strtolower($_REQUEST['type']);
	$manu = $_REQUEST['manu'];
	$regex = '(^SN-[0-9a-z]{32}$)';	
	$regex2 = '(^[0-9a-z]{32}$)';
	$devices = $db->grabAllDT();
	$manus = $db->grabAllManu();
	if(!in_array($manu, $manus)){
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output['Status'] = "Error";
		$output['MSG'] = "Invalid Manufacturer named: $manu. (Not in database)";
		$responseData = json_encode($output);
		echo $responseData;
		die();
	}
	else{
		if(!empty($manu)){
			$result = $dblink->query("select id from manufacturer where manufacturer='$manu'");
			$manustr=$manu;
			while($res = $result->fetch_array(MYSQLI_ASSOC)){
				$manu=$res['id'];
			}
		}
	}
	if(!in_array($type, $devices)){
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output['Status'] = "Error";
		$output['MSG'] = "Invalid Device Type named: $type. (Not in database)";
		$responseData = json_encode($output);
		echo $responseData;
		die();
	}
	else{
		if(!empty($type)){
			$result = $dblink->query("select id from device_type where d_type='$type'");
			$typestr=$type;
			while($res = $result->fetch_array(MYSQLI_ASSOC)){
				$type=$res['id'];
			}
				
		}
	}
	if(preg_match($regex, $sn) || preg_match($regex2, $sn)){
		if (preg_match($regex2,$sn)){
			$sn = "SN-".$sn;
		}
		if(!($db->insertDevice($manu,$type,$sn))){
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
				$output['MSG'] = "Unable to create device due to duplicate key Serial Number!";
				$responseData=json_encode($output);
				echo $responseData;
				die();
			}
		}
		else{
			header('Content-Type: application/json');
			header('HTTP/1.1 200 OK');
			$output['Status'] = "Success";
			$output['MSG'] = "Device $sn successfully created!";
			$output['Serial Number'] = $sn;
			$output['Device Type'] = $typestr;
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
		$output['MSG']="Invalid serial number entered. SN-(32 lowercase letters or numbers) or (32 lowercase letters or numbers)";
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}
}
else{
	$err='';
	if(!isset($_REQUEST['sn'])){
		$err.="Serial Number (sn=)";
	}
	if(!isset($_REQUEST['manu'])){
		if($err == ""){
			$err.="Manufacturer (manu=)";
		}
		else{
			$err.=", Manufacturer (manu=)";
		}
	}
	if(!isset($_REQUEST['type'])){
		if($err == ""){
			$err.="Device Type (type=)";
		}
		else{
			$err.=", and Device Type (type=)";
		}
	}
	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output['Status']="Invalid Data";
	$output['MSG']="Error with $err. Endpoint Example: CreateDevice&sn={sn}&manu={manu}&type={type}";
	$responseData=json_encode($output);
	echo $responseData;
	die();
}
?>
