<?php
$db = new Database("webuser","ZJ3eR63jZpLo47ER","ase","localhost");
$dblink = $db->setupConnection();
if(isset($_REQUEST['sn']) && isset($_FILES['upload'])){
	$sn = $_REQUEST['sn'];
	$regex = '(^SN-[0-9a-z]{32}$)';
	$regex2 = '(^[0-9a-z]{32}$)';
	if(preg_match($regex,$sn) || preg_match($regex2,$sn)){
		if(preg_match($regex2,$sn)){
			$sn = "SN-".$sn;
		}
		if(!$result = $dblink->query("select id from equip where serial_num='$sn'")){
			header('Content-Type: application/json');
			header('HTTP/1.1 500 Internal Server Error');
			$output['Status']="Internal Server Error";
			$output['MSG']="Internal Server Error.";
			$responseData = json_encode($output);
			echo $responseData;
			die();
		}
		if($result->num_rows > 0){
			while($res = $result->fetch_array(MYSQLI_ASSOC)){
				$id = $res['id'];
			}
			$dir = '/var/www/html/files/'.$id.'/';
			if(!is_dir($dir)){
				mkdir($dir);
			}
			$fileName = str_replace(" ","_",$_FILES['upload']['name']);
			$tmpName = $_FILES['upload']['tmp_name'];
			$fileSize = $_FILES['upload']['size'];
			$fileType = $_FILES['upload']['type'];
			$path = $dir.str_replace(" ","_",$fileName);
			if($fileType != 'application/pdf'){
				header('Content-Type: application/json');
				header('HTTP/1.1 200 OK');
				$output['Status']="Invalid Data";
				$output['MSG']="Error with file type! File must be of type PDF.";
				$responseData = json_encode($output);
				echo $responseData;
				die();
			}
			if($fileSize > 2000000){
				header('Content-Type: application/json');
				header('HTTP/1.1 200 OK');
				$output['Status']="Invalid Data";
				$output['MSG']="Error with file size! File must be 2MB or less.";
				$responseData = json_encode($output);
				echo $responseData;
				die();
			}
			if(!($addFile=$db->addFile(addslashes($fileName),$fileType, $fileSize, addslashes($path), $id))){
				header('Content-Type: application/json');
				header('HTTP/1.1 500 Internal Server Error');
				$output['Status']="Internal Server Error";
				$output['MSG']="Internal Server Error";
				$responseData = json_encode($output);
				echo $responseData;
				die();
			}
			else{
				$tmpFile = fopen($tmpName, 'r');
				$content = fread($tmpFile, filesize($tmpName));
				fclose($tmpFile);
				$newFile = fopen($path, 'wb');
				fwrite($newFile, $content);
				fclose($newFile);
				header('Content-Type: application/json');
				header('HTTP/1.1 200 OK');
				$output['Status']="Success";
				$output['MSG']='Successfully uploaded file '.$_FILES['upload']['name'].' for device with serial number: '.$sn;
				$responseData = json_encode($output);
				echo $responseData;
				die();
			}

		}
		else{
			header('Content-Type: application/json');
			header('HTTP/1.1 200 OK');
			$output['Status']="Success";
			$output['MSG']="Device with serial number $sn not found in database! Could not upload file.";
			$responseData = json_encode($output);
			echo $responseData;
			die();
		}
	}
	else{
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output['Status']="Invalid Data";
		$output['MSG']="Invalid Serial Number entered. Please enter SN-(32 lowercase letters or numbers) or just 32 lower case letters or numbers.";
		$responseData = json_encode($output);
		echo $responseData;
		die();
	}	
}
else{
	header("Content-Type: application/json");
	header("HTTP/1.1 200 OK");
	$output['Status']="Invalid Data";
	$output['MSG']="Either no Serial Number or file (or both) not set. Endpoint Example: ?UploadFile&sn={sn}";
	$responseData=json_encode($output);
	echo $responseData;
	die();
}
die();
?>
