


<? 
$sql = "SELECT SUM(shablon = 'videos') as count_vd, SUM(shablon = 'razdel') as count_rz, SUM(shablon = 'staty') as count_st FROM page";
$r = $db->query($sql);
$res1 = $r->fetch(PDO::FETCH_ASSOC);

$sql = "SELECT COUNT(*) as count FROM comment";
$r = $db->query($sql);
$res4 = $r->fetch(PDO::FETCH_ASSOC);

$sql = "SELECT id_url FROM page WHERE shablon != 'staty' AND shablon != 'videos' AND shablon != 'razdel' AND shablon != 'staty_img'";
$r = $db->query($sql);
$res5 = $r->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="h1_text">Статистика</div>

Страниц с видео: <?=$res1['count_vd']?><br>
Разделов: <?=$res1['count_rz']?><br>
Статьи: <?=$res1['count_st']?><br>
Комменты: <?=$res4['count']?><br><br>

Все остальное: <?=count($res5)?><br><br>

