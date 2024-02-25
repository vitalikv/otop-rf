
<script>
$(document).ready(function(){ 


<? // создаем новую статью ?>
$('[add_p]').click(function () {
vivod(); 
var id = $(this).attr("add_p");
$.ajax({
type: "POST",					
url: '/admin/page/components/page/add.php',
data: {"id":id},
beforeSend: function(){ $("#loader").css({"display":"block"}); },
success: function(data){ $("#loader").css({"display":"none"}); 
data = $.parseJSON(data);
$('[id_url_quest]').html(data[0]);
$('[url_quest]').html(data[1]);
$('[title_quest]').html(data[2]);
$('[type_s]').html('новая статья');
}
});
});
<? // --------- ?>



<? // редактируем статью ?>
$('[dsk]').click(function () {
vivod(); 
var id = $(this).attr("dsk");
$.ajax({
type: "POST",					
url: '/admin/page/components/page/upd.php',
data: {"id":id},
beforeSend: function(){ $("#loader").css({"display":"block"}); },
success: function(data){ $("#loader").css({"display":"none"}); 
data = $.parseJSON(data);
$('[url_quest]').html(data[0]);
$('[title_quest]').html(data[1]);
$('[id_url_quest]').html(data[2]); 
$('[h1_quest]').html(data[3]); 
$('[form_quest]').html(data[4]);
$('[type_s]').html('редактирование статьи'); 
}
});
});
<? // --------- ?>


<? // выводим редактор ?>
function vivod(){  
$.ajax({
type: "POST",					
url: '/admin/page/components/page/views.php',
beforeSend: function(){ },
success: function(data){ $('[upd_page]').html(data); }
});    
};
<? // --------- ?>

			
});
</script>

<? 
$sql = "SELECT p.id_url AS id_url, p.url AS url, c.title AS title
FROM content_videos c
INNER JOIN page AS p on p.id_url = c.id_url 
WHERE shablon = 'videos' 
ORDER BY p.id_url";

$r = $db->query($sql);
$res = $r->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="h1_text">Редактировать</div>

<div upd_page="">
<?
foreach ($res as $pg) { ?>
<div class="links_b">
<a href="<?=$pg['url']?>" class="links_1" target="_blank"><?=$pg['url']?></a> 
<div add_p="<?=$pg['id_url']?>" class="links_2"><?=$pg['title']?></div>

<? 
$sql = "SELECT * FROM content_videos_st WHERE id_url_cont = {$pg['id_url']}";
$r = $db->query($sql);
$link = $r->fetchAll(PDO::FETCH_ASSOC);


foreach ($link as $p) {?>
<div dsk="<?=$p['id_cont']?>" class="links_3"><?=$p['h2']?></div>
<?}?>
</div>
<?}?>
<div class="clear"></div>
</div>





