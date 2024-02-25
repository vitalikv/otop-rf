<?php
// скрипт списывает дни в таблице subscription (подключение к бд теплый пол)

header('Content-Type: application/json; charset=utf-8');

require_once ($_SERVER['DOCUMENT_ROOT']."/gl/include/bd_1.php");


// данные на выход
$data = [];
$data['result'] = false;

$sql = "UPDATE subscription SET days = days - 1 WHERE days > 0";
$r = $db->prepare($sql);
$r->execute();

$count = $r->rowCount();

if($count > 0)
{
	$data['result'] = true;
	$data['count'] = $count;
}

// отдаем результат в json
echo json_encode( $data );




