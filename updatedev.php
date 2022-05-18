<?php
$db = new Database("webuser","ZJ3eR63jZpLo47ER","ase","localhost");
$dblink = $db->setupConnection();
if(isset($_REQUEST['sn']) && (isset($_REQUEST['manu']) || isset($_REQUEST['type']) || isset($_REQUEST['newsn']) || isset($_REQUEST['active']))){
	$sn = $_REQUEST['sn'];
	$type = addslashes(strtolower($_REQUEST['type']));
	$manu = addslashes($_REQUEST['manu']);
	$active = addslashes($_REQUEST['active']);
	$newsn = $_REQUEST['newsn'];
	$regex = '(^SN-[0-9a-z]{32}$)';	
	$regex2 = '(^[0-9a-z]{32}$)';
	$devices = $db->grabAllDT();
	$manus = $db->grabAllManu();
	if(!empty($manu) && !in_array($manu, $manus)){
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
	if(!empty($type) && !in_array($type, $devices)){
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
	if(!empty($active) && (strtolower($active) != 'active' && strtolower($active) != 'deactive')){
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output['Status'] = "Error";
		$output['MSG'] = "Invalid active status. (Active can only be active or deactive";
		$responseData = json_encode($output);
		echo $responseData;
		die();

	}
	else{
		if(!empty($active)){
			if(strtolower($active) == 'active'){
				$active = 1;
			}
			else{
				$active = 0;
				$check = 1;
			}
		}	
	}
	if(empty($newsn)){
		$newsn = 1;
	}
	else if(!empty($newsn) && (!preg_match($regex, $newsn) && !preg_match($regex2, $newsn))){
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output['Status']="Invalid Data";
		$output['MSG']="Error! New Serial Number invalid. SN-(32 lowercase letters or numbers) or (32 lowercase letters or numbers)";
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}
	if(preg_match($regex2,$newsn)){
		$newsn="SN-".$newsn;
	}
	if(preg_match($regex, $sn) || preg_match($regex2, $sn)){
		if (preg_match($regex2,$sn)){
			$sn = "SN-".$sn;
		}
		if(empty($manu)){
			$result = $dblink->query("select manufacturer from equip where serial_num='$sn'");
			if($result->num_rows){
				while($res = $result->fetch_array(MYSQLI_ASSOC)){
					$manu=$res['manufacturer'];
				}
			}
			else{
				$manu = -1;
			}
		}
		if(empty($type)){
			$result = $dblink->query("select type from equip where serial_num='$sn'");
			if($result->num_rows){
				while($res = $result->fetch_array(MYSQLI_ASSOC)){
					$type=$res['type'];
				}
			}
			else{
				$type = -1;
			}
		}
		if(empty($active) && !isset($check)){
				$result = $dblink->query("select active from equip where serial_num='$sn'");
				if($result->num_rows){
					while($res = $result->fetch_array(MYSQLI_ASSOC)){
						$active=$res['active'];
					}
				}
				else{
					$active = -1;
				}
		}
		if(!($db->updateDevice($manu,$type,$sn,$active,$newsn))){
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
		else if($db->grabAffected() == 0){
			$result = $dblink->query("select id from equip where serial_num='$sn'");
			if($result->num_rows == 0){
				header('Content-Type: application/json');
				header('HTTP/1.1 200 OK');
				$output['Status']= "Error";
				$output['MSG'] = "Serial Number $sn not found. Nothing updated.";
				$responseData=json_encode($output);
				echo $responseData;
				die();
			}
			else{
				header('Content-Type: application/json');
				header('HTTP/1.1 200 OK');
				$output['Status']= "Success";
				$output['MSG'] = "Nothing updated. (values are the same)";
				$responseData=json_encode($output);
				echo $responseData;
				die();
			}
		}	
		else{
			if(!($newsn == 1)){
				$sn = $newsn;
			}
			header('Content-Type: application/json');
			header('HTTP/1.1 200 OK');
			$output['Status'] = "Success";
			$output['MSG'] = "Device successfully updated!";
			$result = $dblink->query("select d.d_type, e.serial_num, e.active, m.manufacturer from equip e, device_type d, manufacturer m where e.type=d.id and e.manufacturer=m.id and serial_num='$sn'");
			while($res = $result->fetch_array(MYSQLI_ASSOC)){
				$output['Serial Number'] = $res['serial_num'];
				$output['Manufacturer'] = $res['manufacturer'];
				$output['Device Type'] = $res['d_type'];
				if($res['active'] == 1){
					$output['Active?'] = 'Active';
				}	
				else{
					$output['Active?'] = 'Inactive';
				}
			}
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
	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output['Status']="Invalid Data";
	$output['MSG']="Error! No Serial Number inputted or nothing inputted to update. Endpoint Example: ?UpdateDevice&sn={sn}&manu={manu}&type={type}&newsn={newsn} (1 argument required to update).";
	$responseData=json_encode($output);
	echo $responseData;
	die();
}
?>
