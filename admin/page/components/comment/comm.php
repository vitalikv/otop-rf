<?
require_once ($_SERVER['DOCUMENT_ROOT']."/include/bd.php");


$sql = "SELECT p.url AS url, COUNT(c.comment) AS comment
FROM comment c
RIGHT JOIN page AS p on p.id_url = c.id_url
WHERE p.shablon = 'videos' OR p.shablon = 'schema' OR p.shablon = 'videos_1' 
GROUP BY p.url
ORDER BY COUNT(c.comment)";

$cc = $db->query($sql);
$comms = $cc->fetchAll(PDO::FETCH_ASSOC);



$sum_comm = count($comms);
?>
<? // комменты ?>


<div>Комментарии (<?=$sum_comm?>)</div>



<? foreach ($comms as $comm) { $arr[] = $comm['comment']; } ?>


<? 
foreach ($comms as $comm) { 
if($sv!=$comm['comment']) { echo $comm['comment'].'<br>'; } 
?>
<div>http://xn------6cdcklga3agac0adveeerahel6btn3c.xn--p1ai<?=$comm['url']?></div>
<? 
$sv = $comm['comment']; 
}?>





