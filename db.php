<?php
class Database {
	private $username;
	private $password;
	private $dbName;
	private $hostName;
	private $connection;
	private $error;
	private $affected;

	function __construct($un, $pw, $dbn, $hn){
		$this->username=$un;
		$this->password=$pw;
		$this->dbName=$dbn;
		$this->hostName=$hn;
	}

	function grabMembers(){
		return $this->username.$this->password.$this->dbName.$this->hostName;
	}

	function setupConnection(){
		$this->connection=new mysqli($this->hostName,$this->username,$this->password,$this->dbName);
		return $this->connection;
	}

	function grabAllDT(){
		$sql='select d.d_type from device_type d';
		$result=$this->connection->query($sql) or
			die('Something went wrong when fetching device types ERROR: '.$this->connection->error);
		$devices=array();
		while($data=$result->fetch_array(MYSQLI_ASSOC)){
			$devices[]=$data['d_type'];
		}
		return $devices;
	}

	function grabAllManu(){
		$sql='select m.manufacturer from manufacturer m';
	        $result=$this->connection->query($sql) or
			die('Something went wrong when fetching manufacturers ERROR: '.$this->connection->error);
		$manu=array();                                		
	        while($data=$result->fetch_array(MYSQLI_ASSOC)){
			$manu[]=$data['manufacturer'];
		}
		return $manu;	
	}

	function grabFilesById($id){
		$sql="select file_name from file where device=$id";
		if(!$results=$this->connection->query($sql)){
			return -1;
		}
		if($results->num_rows == 0){
			return 0;
		}
		else{
			$files=array();
			while($data=$results->fetch_array(MYSQLI_ASSOC)){
				$files[]=$data['file_name'];
			}
			return $files;
		}
		
	}

	function grabFilesBySN($sn){
		$sql="select f.file_name from equip e, file f where e.serial_num='$sn' and e.id=f.device";
		if(!$results=$this->connection->query($sql)){
			return -1;
		}
		if($results->num_rows == 0){
			return 0;
		}
		else{
			$files=array();
			while($data=$results->fetch_array(MYSQLI_ASSOC)){
				$files[]=$data['file_name'];
			}
			return $files;
		}
	}

	function insertDevice($manu, $dt, $sn){
		$sql="insert into equip(type,manufacturer,serial_num) values($dt, $manu, '$sn')";
		if(!($add=$this->connection->query($sql))){
			$this->error=$this->connection->error;
			return 0;
		}
		else{
			return 1;
		}
	}

	function updateDevice($manu, $dt, $sn, $active, $newsn){
		if($newsn == 1){
			$sql="update equip set type=$dt, manufacturer=$manu, active='$active' where serial_num='$sn'";
		}
		else{
			$sql="update equip set type=$dt, manufacturer=$manu, active='$active', serial_num='$newsn' where serial_num='$sn'";
		}
		if(!($add=$this->connection->query($sql))){
			$this->error=$this->connection->error;
			return 0;
		}
		else{
			$this->affected = $this->connection->affected_rows;
			return 1;
		}
	}

	function insertDT($dt){
		$sql="insert into device_type(d_type) values('$dt')";
		if(!($add=$this->connection->query($sql))){
			$this->error=$this->connection->error;
			return 0;
		}
		else{
			return 1;
		}
	}

	function insertManu($manu){
		$sql="insert into manufacturer(manufacturer) values('$manu')";
		if(!($add=$this->connection->query($sql))){
			$this->error=$this->connection->error;
			return 0;
		}
		else{
			return 1;
		}
	}

	function deleteDevice($sn){
		$sql="delete from equip where serial_num='$sn'";
		if(!($delete=$this->connection->query($sql))){
			$this->error=$this->connection->error;
			return 0;
		}
		else{
			if($this->connection->affected_rows > 0){
				return 1;
			}
			else{
				return 2;
			}
		}
	}

	function addFile($fileName, $fileType, $fileSize, $path, $id){
		$sql = "insert into file(file_name, file_type, file_size, file_path, device) values('".$fileName."','".$fileType."',".$fileSize.",'".$path."',".$id.")";
		if(!($addFile=$this->connection->query($sql))){
			$this->error=$this->connection->error;
			return 0;
		}
		else{
			return 1;
		}
	}

	function readDev($sn){
		$result = $this->connection->query("select d.d_type, e.serial_num, e.active, m.manufacturer from equip e, device_type d, manufacturer m where e.type=d.id and e.manufacturer=m.id and serial_num='$sn'");
		if($result->num_rows == 0){
			return 0;
		}
		else{
			while($res = $result->fetch_array(MYSQLI_ASSOC)){
				$arr['Serial Number'] = $res['serial_num'];
				$arr['Manufacturer'] = $res['manufacturer'];
				$arr['Device Type'] = $res['d_type'];
				if($res['active'] == 1){
					$arr['Active?'] = 'Active';
				}
				else{
					$arr['Active?'] = 'Inactive';
				}
			}
		}
		return $arr;
	}


	function grabErrno(){
		return $this->connection->errno;
	}

	function grabAffected(){
		return $this->affected;
	}

	function displayError(){
		return $this->error;
	}

}
?>
