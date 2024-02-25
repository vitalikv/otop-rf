<?php
require_once ($_SERVER['DOCUMENT_ROOT']."/include/bd.php");

$id = $_POST['id'];


$sql = "SELECT p.url AS url, c.title AS title
FROM content_videos c
INNER JOIN page AS p on p.id_url = c.id_url 
WHERE p.id_url = {$id} 
LIMIT 1";

// $sql = "SELECT url, title FROM page WHERE id_url = {$id} LIMIT 1";
$r = $db->prepare($sql);
$r->bindValue(':id', $id);
$r->execute();
$res = $r->fetch(PDO::FETCH_ASSOC);



echo json_encode(array($id ,$res['url'],$res['title']));



?>





