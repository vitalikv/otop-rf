

<link rel="stylesheet" href="/admin/css/comment.css?1">


<script>
$(document).ready(function(){ 


<? //  ?>
$('[butt_2]').click(function () { 
var id = $(this).attr("butt_2");
$.ajax({
type: "POST",					
url: '/admin/page/components/comment/comm.php',
data: {"id":id},
beforeSend: function(){ $("#loader").css({"display":"block"}); },
success: function(data){ $("#loader").css({"display":"none"}); 
$('[bl_cont]').html(data);
}
});
});
<? // --------- ?>



<? // delet коммент ?>
$('[comm_delet]').click(function () { 
var id = $(this).attr('comm_delet');
$.ajax({
type: "POST",					
url: '/admin/page/components/comment/comm_delet.php',
data: {"id":id},
beforeSend: function(){ $("#loader").css({"display":"block"}); },
success: function(data){ $("#loader").css({"display":"none"}); 
$('[comm_id="'+id+'"]').remove();
}
});
});
<? // delet коммент ?>

			
});
</script>


<div>
Все
</div>

<div butt_2="">
По убывнию
</div>

<div>
По страницам
</div>


<? // комменты ?>
<?
$sql = "SELECT * FROM comment ORDER BY id DESC";

$sql = "SELECT c.*, p.url AS url
FROM comment c
INNER JOIN page AS p on p.id_url = c.id_url  
ORDER BY c.id DESC LIMIT 20";

$cc = $db->query($sql);
$comms = $cc->fetchAll(PDO::FETCH_ASSOC);


$sum_comm = count($comms);
?>
<? // комменты ?>


<div bl_cont="">

<div>Комментарии (<?=$sum_comm?>)</div>

<?
foreach ($comms as $comm) { ?>

<div comm_id="<?=$comm['id']?>" comm_gr="<?=$nm_pr?>" class="comm_text_1">
<div class="comm_t_name"><?=$comm['name']?></div>
<div class="comm_t_answer"><?=$otvet?></div> 
<div class="comm_t_date"><?=$comm['date']?></div> 
<a href="<?=$comm['url']?>" class="comm_links_p" target="_blank"><?=$comm['url']?></a> <br>   <? // ссылка на страницу с комментом ?>
<div class="comm_t_cont"><?=$comm['comment']?></div>
<div comm_ans="<?=$nm_pr?>" attr_pr="<?=$comm['id']?>" class="comm_ans">Ответить</div>
<div comm_delet="<?=$comm['id']?>" class="comm_delet"></div>
</div>

<?}?>

</div>



