<? 
require_once ($_SERVER['DOCUMENT_ROOT']."/include/bd.php");


// админ панель
if (!isset($_COOKIE['pass'])) { $ad_enter = 'no'; }
else { 
$val = $_COOKIE['pass'];
$sql = "SELECT * FROM users WHERE pass=:pass LIMIT 1";
$r = $db->prepare($sql);
$r->bindValue(':pass', $val);
$r->execute();
$user = $r->fetch(PDO::FETCH_ASSOC);
if($user['id']!=='') { $ad_enter = 'yes'; }
}
// админ панель


 
$url = $_SERVER['REQUEST_URI'];


// выбираем страницу по url, подставляем шаблон 
$sql = "SELECT * FROM page WHERE url='$url' LIMIT 1";
$r = $db->query($sql);
$page = $r->fetch(PDO::FETCH_ASSOC);

set_page($page['id_url']);


$id_tabl = $page['shablon'];
if($id_tabl !='videos'){ $id_tabl = 'page'; }

if($id_tabl =='videos'){ $dpt = ' | Видео'; }

$sql = "SELECT * FROM content_{$id_tabl} WHERE id_url={$page['id_url']} LIMIT 1";
$r = $db->query($sql);
$cont = $r->fetch(PDO::FETCH_ASSOC);


// проверка, существует ли стр
function set_page($page){ if($page=='') { header("HTTP/1.1 404 Not Found"); include_once($_SERVER['DOCUMENT_ROOT']."/include/404.php"); exit; } }

?>


<!DOCTYPE html>
<html lang="en">
<head>
<title><?=$cont['title']?><?=$dpt?></title>
<meta charset="utf-8">
<link rel="icon" href="/img/favicon.ico">
<meta name="description" content="<?=$cont['descr']?>" />
<meta name='yandex-verification' content='75f0355671ff6bef' />
<meta name="google-site-verification" content="0Oe0BQaLHkPIGeyOd37evo7Z5lkknWfIiwCMQvRyx1M" />
<meta name="google-site-verification" content="rxPpncTZnV61lEll7tPg0YrpVma9frjPo2P0djQauvk" /> <? // youtube ?>
<link rel="stylesheet" href="/css/reset.css">
<link rel="stylesheet" href="/css/style.css?12">
<link rel="stylesheet" href="/css/css_comment.css?3">
<script src="/js/jquery.js"></script>
<script src="/js/cookie.js"></script>

</head>
<body>

<div class="contener">

<script>console.log(window.location.hostname)</script>

<? include($_SERVER['DOCUMENT_ROOT']."/include/block1.php");  ?>

<div class="content">
<? include($_SERVER['DOCUMENT_ROOT']."/page/".$page['shablon'].".php");  ?>
</div>
<? include($_SERVER['DOCUMENT_ROOT']."/include/block2.php");   ?>

</div>

</body>
</html>

