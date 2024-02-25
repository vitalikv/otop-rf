<?php
require_once ($_SERVER['DOCUMENT_ROOT']."/include/bd.php");

$flag = $_POST['flag'];
$id = $_POST['id'];
$date = $_POST['date'];
$comment = $_POST['comment'];


if($flag == 'upd_comm'){
$date = preg_replace('|<.+>|isU','',$date);
$date = trim($date);
if (!preg_match("|^\d{4}-\d{2}-\d{2}-\d{1,2}-\d{2}$|Usi", $date)) { $inf = 'error'; }
$comment = work_text($comment);

if($inf != 'error'){
$sql = "UPDATE comment SET date = :date, comment = :comment WHERE id = :id";
$r = $db->prepare($sql);
$r->bindValue(':id', $id);
$r->bindValue(':date', $date);
$r->bindValue(':comment', $comment);
$f = $r->execute();
if($f=='1'){ $inf = 'success'; } // проверяем записались ли данные
}

if($inf == 'success'){
$b = russian_date_comm($date);
$a = 'ок'; 
} 
else { $a = 'ошибка'; $b=''; }

echo json_encode(array($a,$b));
}


if($flag == 'select_data'){  
// $sql = "SELECT date FROM comment WHERE id = {$id} LIMIT 1";
// $r = $db->query($sql);
// $res = $r->fetch(PDO::FETCH_ASSOC);

$r = $db->prepare("SELECT date FROM comment WHERE id = ? LIMIT 1");
$r->bindValue(1, $id, PDO::PARAM_INT);
$r->execute();
$res = $r->fetch(PDO::FETCH_ASSOC);
echo $res['date'];
}


?>


<?
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
$text = preg_replace('|<br>|isU','[br]',$text);
$text = preg_replace('|<.+>|isU','',$text);
$text = preg_replace('|\[br\]|isU','<br>',$text);
$text = preg_replace('|&nbsp;|i', '', $text); 
$text = preg_replace('|\s{2,}|isU','',$text);
return $text;
}


function russian_date_comm($time){
$date=explode("-", $time);
switch ($date[1]){
case 1: $m='января'; break;
case 2: $m='февраля'; break;
case 3: $m='марта'; break;
case 4: $m='апреля'; break;
case 5: $m='мая'; break;
case 6: $m='июня'; break;
case 7: $m='июля'; break;
case 8: $m='августа'; break;
case 9: $m='сентября'; break;
case 10: $m='октября'; break;
case 11: $m='ноября'; break;
case 12: $m='декабря'; break;
}
$day_d = (integer)$date[2];

$today = date("Y-m-d-G-i-s");
$today = explode("-", $today);

$day = $today[2]-$date[2];
$month = $today[1]-$date[1];
$year = $today[0]-$date[0];

if($year >0) {$month = $month + 13;} // 13 - потому что, при добавление к отрицательному числу, мы пересекаем еще 0

if($month >8) {$vr = $day_d.' '.$m.' '.$date[0];}
elseif($month >3) {$vr = $day_d.' '.$m;}
elseif($month >0) {$vr = $day_d.' '.$m.' в '.$date[3].':'.$date[4];}
elseif($day >1) {$vr = $day_d.' '.$m.' в '.$date[3].':'.$date[4];}
elseif($day ==1) {$vr = 'вчера в '.$date[3].':'.$date[4];}
elseif($day ==0) {$vr = 'сегодня в '.$date[3].':'.$date[4];}
else {}

return $vr;
}
?>









