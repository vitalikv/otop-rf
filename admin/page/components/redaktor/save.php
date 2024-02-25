<?php
require_once ($_SERVER['DOCUMENT_ROOT']."/include/bd.php");

$url = $_POST['url'];
$razdel = $_POST['razdel'];
$id_url = $_POST['id_url'];
$title = $_POST['title'];
$h1 = $_POST['h1'];  
$descr = $_POST['descr'];
$text = $_POST['text'];
$img = $_POST['img'];

$tabl='content_page'; 

$url = preg_replace('|<.+>|iU',' ',$url);
$title = preg_replace('|<.+>|iU',' ',$title);
$h1 = preg_replace('|<.+>|iU',' ',$h1);
$descr = preg_replace('|<.+>|iU',' ',$descr);

$title = work_text($title);
$h1 = work_text($h1);
$descr = work_text($descr);
$text = work_text($text);

function work_text($text){
$text = trim($text);
if (preg_match('/class="?Mso|style="[^"]*\bmso-|style=\'[^\'\']*\bmso-|w:WordDocument/', $text)) {
$text = preg_replace('|<br>|isU','[br]',$text);
$text = preg_replace('|<\/p>|isU','[br]',$text);
$text = preg_replace('|\n|isU',' ',$text);
$text = preg_replace('|<!--[\s\S]+-->|isU','',$text);
$text = preg_replace('|<.+>|isU','',$text);
$text = preg_replace('|\[br\]|isU','<br>',$text);
}
$text = preg_replace('|&nbsp;|i', '', $text); 
$text = preg_replace('|\s{2,}|isU','',$text);
return $text;
}



if($id_url != 'new'){
// обновляем статью
$sql = "UPDATE {$tabl} SET title = :title, h1 = :h1, descr = :descr, content = :text, img = :img WHERE id = :id";
$r = $db->prepare($sql);
$r->bindValue(':id', $id_url);
$r->bindValue(':title', $title);
$r->bindValue(':h1', $h1);
$r->bindValue(':descr', $descr);
$r->bindValue(':text', $text);
$r->bindValue(':img', $img);
$r->execute();
echo 'ok';
// обновляем статью
}else{
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
$sql = "INSERT INTO {$tabl} (id_url, title, h1, descr, content, img) VALUES ( :id_url, :title, :h1, :descr, :text, :img)";
$r = $db->prepare($sql);
$r->bindValue(':id_url', $last_id);
$r->bindValue(':title', $title);
$r->bindValue(':h1', $h1);
$r->bindValue(':descr', $descr);
$r->bindValue(':text', $text);
$r->bindValue(':img', $img);
$r->execute();
echo '<a href="'.$url.'" target="_blank">'.$title.'</a>';
}
// записываем данные
}



?>