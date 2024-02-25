

<script>
$(document).ready(function(){ 


<? // выводим редактор ?>
$('[dsk]').click(function () {
var id = $(this).attr("dsk");  
$.ajax({
type: "POST",					
url: '/admin/page/components/redaktor/views.php',
data: {"id":id, "razdel":"<?=$met_1?>", "opisan_1":"<?=$opisan_1?>", "opisan_2":"<?=$opisan_2?>"},
beforeSend: function(){ $("#loader").css({"display":"block"}); },
success: function(data){ $("#loader").css({"display":"none"}); $('[wysiwyg]').html(data); }
});    
});
<? // --------- ?>

			
});
</script>


<? 
if($met_1=='staty'){
$sql = "SELECT c.id AS id, title
FROM content_page c
INNER JOIN page AS p on p.id_url = c.id_url
WHERE p.shablon = 'staty'
ORDER BY p.id_url DESC";
}
else {
$sql = "SELECT c.id AS id, title
FROM content_page c
INNER JOIN page AS p on p.id_url = c.id_url
WHERE p.shablon != 'staty'
ORDER BY p.id_url DESC";
}

$r = $db->query($sql);
$res = $r->fetchAll(PDO::FETCH_ASSOC);
?>

<div wysiwyg="">
<div class="h1_text">Редактировать <?=$opisan_2?></div>
<?foreach ($res as $text) {?>
<div class="links-b">
<div dsk="<?=$text['id']?>" class="links_a"><?=$text['title']?></div>
</div>
<?}?>
<div class="clear"></div>
</div>




