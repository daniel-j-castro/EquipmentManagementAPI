<?php
$db = new Database("webuser","ZJ3eR63jZpLo47ER","ase","localhost");
$dblink = $db->setupConnection();
$pointer = $_REQUEST['pointer'];
$direction = $_REQUEST['']
$rpp = 10;
$sql = "select serial_num, id from equip where id > $pointer order by id limit $rpp";
$result = $dblink->query($sql);
$count = mysqli_fetch_array($dblink->query("select count(id) from equip where type=1"))[0];
$c = 0;
while($res = $result->fetch_array(MYSQLI_ASSOC)){
	echo $res['serial_num']."<br>";
	$c += 1;
	if($c == $result->num_rows){
		$nextPointer = 'select serial_num, id from equip where id > '.$res['id'].' order by id limit '.$rpp;
	}
}
if($pointer == 0){
	$previousPointer = 'select serial_num, id from equip where id < 0 order by id limit '.$rpp;
}
else{
	if ($pointer < 0){
		$pointer = 0;
	}
	$previousPointer = 'select serial_num, id from equip where id < '.$pointer.' order by id limit '.$rpp;	
}
echo $count.'<br>';
echo "Next = URL/?SearchDevices&pointer=$pointer&direction=next&conditions=computeretc. <br>";
echo "Previous = URL/?SearchDevices&pointer=$pointer&direction=previous&conditions=computeretc.";
die();
?>
