<?
// ---> news.php




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

if($month >8) {echo $day_d.' '.$m.' '.$date[0];}
elseif($month >3) {echo $day_d.' '.$m;}
elseif($month >0) {echo $day_d.' '.$m.' в '.$date[3].':'.$date[4];}
elseif($day >1) {echo $day_d.' '.$m.' в '.$date[3].':'.$date[4];}
elseif($day ==1) {echo 'вчера в '.$date[3].':'.$date[4];}
elseif($day ==0) {echo 'сегодня в '.$date[3].':'.$date[4];}
else {}

}


?>

