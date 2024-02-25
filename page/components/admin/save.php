<?php
require_once ($_SERVER['DOCUMENT_ROOT']."/include/bd.php");

$id_url = $_POST['id_url'];
$title = $_POST['title'];
$h1 = $_POST['h1'];  
$descr = $_POST['descr'];
$text = $_POST['text'];
$youtube = $_POST['youtube'];


$title = preg_replace('|<.+>|iU',' ',$title);
$h1 = preg_replace('|<.+>|iU',' ',$h1);
$descr = preg_replace('|<.+>|iU',' ',$descr);
$youtube = preg_replace('|<.+>|iU',' ',$youtube);

$title = work_text($title);
$h1 = work_text($h1);
$descr = work_text($descr);
$text = work_text($text);
$youtube = work_text($youtube);

function work_text($text){
$text = trim($text);
// если текст вставлен из Word, то удаляются все теги
if (preg_match('/class="?Mso|style="[^"]*\bmso-|style=\'[^\'\']*\bmso-|w:WordDocument/', $text)) {
$text = preg_replace('|<br>|isU','[br]',$text);
$text = preg_replace('|<\/p>|isU','[br]',$text);
$text = preg_replace('|\n|isU',' ',$text);
$text = preg_replace('|<!--[\s\S]+-->|isU','',$text);
$text = preg_replace('|<.+>|isU','',$text);
$text = preg_replace('|\[br\]|isU','<br>',$text);
}
// ---
// $text = preg_replace('|<.+>|isU','',$text);
// $text = preg_replace('|<\\.+>|isU','',$text);
$text = preg_replace('|&nbsp;|i', '', $text); 
$text = preg_replace('|\s{2,}|isU','',$text);
return $text;
}



// echo $id_url.'<br> '.$title.'<br> '.$h1.'<br> '.$descr.'<br> '.$text.'<br> '.$youtube;
 


$sql = "UPDATE content_videos SET title = :title, h1 = :h1, descr = :descr, youtube = :youtube, content = :text WHERE id_url = :id_url";
$r = $db->prepare($sql);
$r->bindValue(':id_url', $id_url);
$r->bindValue(':title', $title);
$r->bindValue(':h1', $h1);
$r->bindValue(':descr', $descr);
$r->bindValue(':youtube', $youtube);
$r->bindValue(':text', $text);
$r->execute();

echo 'ok';







?>





