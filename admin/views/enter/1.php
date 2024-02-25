<?

require_once ($_SERVER['DOCUMENT_ROOT']."/include/bd.php");


$val = $_POST['val'];
$val = trim($val);


$sql = "SELECT * FROM users WHERE pass=:pass LIMIT 1";
$r = $db->prepare($sql);
$r->bindValue(':pass', $val);
$r->execute();
$res = $r->fetch(PDO::FETCH_ASSOC);


if($res['id']=='') { $a = '0'; $b = 'Неверный пароль'; }
else { $a = '1'; $b = $val; }


echo json_encode(array($a,$b));


?>