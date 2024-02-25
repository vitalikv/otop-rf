<?php
//require_once ($_SERVER['DOCUMENT_ROOT']."/gl/include/bd_1.php");
require_once ("../../include/bd_1.php");


$id = trim($_POST['id']);
$name = trim($_POST['name']);
$type = trim($_POST['type']);
$model = trim($_POST['model']);
$params = trim($_POST['params']); 
//$date = date("Y-m-d-G-i");


	

if($id == 0)
{
	$sql = "INSERT INTO list_obj_3 (name, type, params) VALUES (:name, :type, :params)";

	$r = $db->prepare($sql);
	$r->bindValue(':name', $name);
	$r->bindValue(':type', $type);
	$r->bindValue(':params', $params);
	$r->execute();


	$count = $r->rowCount();

	if($count==1)
	{ 
		$inf['success'] = true;
		$inf['id'] = $db->lastInsertId(); 	
	}
}

echo json_encode( $inf );

?>





