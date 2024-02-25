<?php
require_once ($_SERVER['DOCUMENT_ROOT']."/include/bd.php");

$url = $_POST['url'];
$url = trim($url);


// проверяем наличие такого же url 
$sql = "SELECT url FROM page WHERE url = :url";
$r = $db->prepare($sql);
$r->bindValue(':url', $url);
$r->execute();
$set_url = $r->fetch(PDO::FETCH_ASSOC);
if(count($set_url['url'])>0){ $err = 'Занят'; }
else { $err = 'Свободен'; }

echo json_encode(array($err));




?>





