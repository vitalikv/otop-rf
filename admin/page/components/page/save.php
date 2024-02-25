<?php
require_once ($_SERVER['DOCUMENT_ROOT']."/include/bd.php");

$type_s = $_POST['type_s'];
$url = $_POST['url'];
$h1 = $_POST['h1'];  
$text = $_POST['text'];



$h1 = preg_replace('|<.+>|isU','',$h1);
$h1 = work_text($h1);
$text = work_text($text);

function work_text($text){
$text = trim($text);
$text = preg_replace('|&nbsp;|i', '', $text); 
$text = preg_replace('|\s{2,}|isU','',$text);
return $text;
}


if($type_s == 'новая статья'){ $sql = "INSERT INTO content_videos_st (id_url_cont, h2, content) VALUES ( :url, :h1, :text)"; }
if($type_s == 'редактирование статьи'){ $sql = "UPDATE content_videos_st SET h2 = :h1, content = :text WHERE id_cont = :url"; }

$r = $db->prepare($sql);
$r->bindValue(':url', $url);
$r->bindValue(':h1', $h1);
$r->bindValue(':text', $text);
$r->execute();


$count = $r->rowCount();

if($count==1){ echo 'Страница создана'; }
else{ echo 'Ошибка'; }





?>





