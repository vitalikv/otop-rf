<?php
require_once ($_SERVER['DOCUMENT_ROOT']."/include/bd.php");


$url = $_POST['url'];
$razdel = $_POST['razdel'];
$title = $_POST['title'];
$h1 = $_POST['h1'];  


$url = work_text($url);
$title = work_text($title);
$h1 = work_text($h1);

function work_text($text){
$text = trim($text);
$text = preg_replace('|&nbsp;|i', '', $text); 
$text = preg_replace('|\s{2,}|isU','',$text);
$text = preg_replace('|<.+>|isU','',$text);
return $text;
}








// записываем новый URL
$sql = "INSERT INTO page (url, shablon) VALUES ( :url, :shablon)";
$r = $db->prepare($sql);
$r->bindValue(':url', $url);
$r->bindValue(':shablon', $razdel);
$r->execute();
$last_id = $db->lastInsertId();
// записываем новый URL


// записываем данные
if($last_id>0){
$sql = "INSERT INTO content_videos (id_url, title, h1) VALUES ( :id_url, :title, :h1)";
$r = $db->prepare($sql);
$r->bindValue(':id_url', $last_id);
$r->bindValue(':title', $title);
$r->bindValue(':h1', $h1);
$r->execute();

$count = $r->rowCount();

if($count==1){ echo 'Страница создана'; }
else{ echo 'Ошибка 2'; }
}
else{ echo 'Ошибка 1'; }
// записываем данные



?>





