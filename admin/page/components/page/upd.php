<?php
require_once ($_SERVER['DOCUMENT_ROOT']."/include/bd.php");

$id = $_POST['id'];


$sql = "SELECT p.url AS url, v.title AS title, c.h2 AS h2, c.content AS content
FROM content_videos_st c
INNER JOIN page AS p on p.id_url = c.id_url_cont 
INNER JOIN content_videos AS v on v.id_url = p.id_url 
WHERE c.id_cont = {$id} 
LIMIT 1";
$r = $db->query($sql);
$abs = $r->fetch(PDO::FETCH_ASSOC);


echo json_encode(array($abs['url'],$abs['title'],$id ,$abs['h2'],$abs['content']));



?>





