<? 

require_once ($_SERVER['DOCUMENT_ROOT']."/include/bd.php");

if (!isset($_COOKIE['pass'])) { require_once ($_SERVER['DOCUMENT_ROOT']."/admin/views/enter.php"); }
else { 

$val = $_COOKIE['pass'];
$sql = "SELECT * FROM users WHERE pass=:pass LIMIT 1";
$r = $db->prepare($sql);
$r->bindValue(':pass', $val);
$r->execute();
$user = $r->fetch(PDO::FETCH_ASSOC);

if($user['id']=='') { require_once ($_SERVER['DOCUMENT_ROOT']."/admin/views/enter.php"); }
else { require_once ($_SERVER['DOCUMENT_ROOT']."/admin/views/page_1.php"); }
}

?>
