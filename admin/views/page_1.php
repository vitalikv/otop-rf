<?

$url = $_SERVER['REQUEST_URI'];
$u = explode("/", $url);
if($u[2]==''){ $url = 'index'; }
elseif ($u[2]=='staty_add'||$u[2]=='page_add') { $url = 'add'; $opisan_1 = 'Добавить '; }
elseif ($u[2]=='staty_upd'||$u[2]=='page_upd') { $url = 'upd'; $opisan_1 = 'Редактировать '; }
else { $url = $u[2]; }

if ($u[2]=='staty_add'||$u[2]=='staty_upd') { $met_1 = 'staty'; $opisan_2 = 'инфо-статью'; }
if ($u[2]=='page_add'||$u[2]=='page_upd') { $met_1 = 'page'; $opisan_2 = 'page'; }


?>


<!DOCTYPE html>
<html lang="en">
<head>
<title>Редактор</title>
<meta charset="utf-8">
<link rel="icon" href="/img/favicon.ico">	 
<link rel="stylesheet" href="/admin/css/reset.css">
<link rel="stylesheet" href="/admin/css/style.css">
<script src="/admin/js/jquery.js"></script>
</head>
<body>

<div id='loader'><img src="/admin/img/loader.GIF" class="loader"></div>

<div class="fon" fon=""></div>
<div class="okno" okno="">
<div class="okno_close"><div class="close_butt" close_butt=""></div></div>
<div class="okno_content" okno_content=""></div>
</div>

<div class="contener">


<? // general ?>


<div class="tbl">

<div class="tbl_col_1">
<div class="menu">
<a href="/upr/">Главная</a>
<div class="menu_gl">page</div>
<a href="/upr/videos_new">Добавить страницу</a>
<a href="/upr/videos_staty">Статьи к page</a>
<div class="menu_gl">comment</div>
<a href="/upr/comm">Комменты</a>
<div class="menu_gl">staty</div>
<a href="/upr/staty_add">Добавит статью</a>
<a href="/upr/staty_upd">Редактор статьи</a>
<div class="menu_gl">page</div>
<a href="/upr/page_add">Добавит статью</a>
<a href="/upr/page_upd">Редактор статьи</a>
<div class="menu_gl">cdm</div>
<a href="/upr/cdm">комм 1</a>
</div>
</div>

<div class="tbl_col_2">
<div class="content">
<? include($_SERVER['DOCUMENT_ROOT']."/admin/page/".$url.".php");  ?>
</div>
</div>

</div>


<? // general ?>

</div>

</body>
</html>