<?php
require("db.php");
$uri = parse_url($_SERVER['REQUEST_URI'],PHP_URL_QUERY);
$uri = explode('&', $uri);
$endPoint = $uri[0];
switch($endPoint){
	case "CreateDevice":
		include("createdev.php");
		break;
	case "CreateDeviceType":
		include("createtype.php");
		break;
	case "CreateManufacturer":
		include("createmanu.php");
		break;
	case "ReadDevice":
		include("readdev.php");
		break;
	case "ViewDeviceFiles":
		include("viewfiles.php");
		break;
	case "UpdateDevice":
		include("updatedev.php");
		break;
	case "UploadFile":
		include("uploadfile.php");
		break;
	case "SearchDevices":
		include("search.php");
		break;
	case "DeleteDevice":
		include("delete.php");
		break;
	default:
		die();
}
?>
