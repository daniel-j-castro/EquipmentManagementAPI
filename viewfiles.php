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
		$arr = $db->grabFilesBySN($sn);
		if($arr != -1 && $arr != 0){
			if($result = $dblink->query("select id from equip where serial_num='$sn'")){
				while($res = $result->fetch_array(MYSQLI_ASSOC)){
					$id = $res['id'];
				}
			}
			else{
				header('Content-Type: application/json');
				header('HTTP/1.1 500 Internal Server Error');
				$output['Status']="Error";
				$output['MSG']="Internal Server Error";
				$responseData = json_encode($output);
				echo $responseData;
				die();
			}
			header('Content-Type: application/json');
			header('HTTP/1.1 200 OK');
			$output['Status']="Success";
			$output['MSG']="Successfully found file(s) for device $sn.";
			foreach($arr as $value){
				$value = str_replace(" ","_", $value);
				$output[$value] = "https://ec2-44-202-167-169.compute-1.amazonaws.com/files/$id/$value";
			}
			$responseData = json_encode($output);
			echo $responseData;
			die();
		}
		else if($arr == -1){
			header('Content-Type: application');
			header('HTTP/1.1 500 Internal Server Error');
			$output['Status']="Error";
			$output['MSG']="Internal Server Error";
			$responseData = json_encode($output);
			echo $responeData;
			die();
		}
		else{
			header('Content-Type: application/json');
			header('HTTP/1.1 200 OK');
			$output['Status']="Not Found";
			$output['MSG']="Could not find files for device with Serial Number: $sn in database!";
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
	$output['MSG']="No Serial Number provided. Endpoint Example: ?ViewDeviceFiles&sn={sn}";
	$responseData=json_encode($output);
	echo $responseData;
	die();	
}

?>
