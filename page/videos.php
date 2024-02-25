

<script>
$(document).ready(function(){				

<?// статьи ?>
$('[id_cont_h2]').click(function(){ 
var id = $(this).attr("id_cont_h2");	
if( $('[id_cont_text="'+id+'"]').is(":hidden")) {
$('[id_cont_text]').css({"display":"none"});
$('[id_cont_text="'+id+'"]').css({"display":"block"});
}
else { $('[id_cont_text="'+id+'"]').css({"display":"none"}); }
});
<?// статьи ?>


});
</script>


<? if ($_COOKIE['ad_panel']=='yes') {?>
<script>
$(document).ready(function(){				

<?// сохраняем вопрос ?>
$('[save]').click(function(){ 
var id_url = '<?=$cont['id_url']?>';
var title = $('[p_title]').html();
var h1 = $('[p_h1]').html();
var descr = $('[p_descr]').html();
var text = $('[p_text]').html();
var youtube = $('[p_youtube]').html();
if (!(/[а-яА-Я]+/.test(text))) { text = ""; }
var er;
if (/[а-яА-Я]+/.test(title)) { $('[err_1]').html(''); } else { $('[err_1]').html('напишите название статьи'); er = 1;} 
if (/[а-яА-Я]+/.test(h1)) { $('[err_2]').html(''); } else { $('[err_2]').html('напишите заголовок статьи'); er = 1;}  
if (er !== 1) {
$.ajax({
type: "POST",					
url: '/page/components/admin/save.php',
data: {"id_url":id_url, "title":title, "h1":h1, "descr":descr, "text":text, "youtube":youtube},
beforeSend: function(){$("#loader").css({"display":"block"});},
success: function(data){
$("#loader").css({"display":"none"});  
$('[pl_info]').html(data);
// $('[inf]').css({"display":"block"});  
}
});
}  
});


});
</script>
<?}?>




<div class="infotext">


<? // редактор ?>
<? if ($_COOKIE['ad_panel']=='yes') {?>
title<div p_title="" class="pl_inp" contenteditable="true" spellcheck="true"><?=$cont['title']?></div>
h1<div p_h1="" class="pl_inp" contenteditable="true" spellcheck="true"><?=$cont['h1']?></div> 
desc<div p_descr="" class="pl_opis" contenteditable="true" spellcheck="true"><?=$cont['descr']?></div>
описание<div p_text="" class="pl_opis" contenteditable="true" spellcheck="true"><?=$cont['content']?></div>
youtube<div p_youtube="" class="pl_inp" contenteditable="true" spellcheck="true"><?=$cont['youtube']?></div>

<div class="butt_save" save="">Сохранить</div>

<div pl_info=""></div>
<div err_1=""></div>
<div err_2=""></div>
<? // редактор ?>
<?} else {?>

<h1><?=$cont['h1']?></h1>

<? if(!empty($cont['content'])) { ?>
<div class="v_opis"><?=$cont['content']?></div>
<? } ?>

<? } ?>


<? if(1==2){ ?>
<!-- otop_p_top_2 -->
<div class="ads_top">
<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-5657772487610973" data-ad-slot="9912228641" data-ad-format="auto"></ins>
<script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
</div>
<!-- otop_p_top_2 -->
<? } ?>


<? // видео ?>
<div class="v_youtube">
<iframe width="100%" height="100%" src="//www.youtube.com/<?=$cont['youtube']?>" frameborder="0" allowfullscreen></iframe>
</div>
<? // видео ?>




<? // eng-plan ?>
<div class="ads_370x320">
	<a href="https://engineering-plan.ru/" class="engineering_1" target="_blank">
		<img src="/img/staty/prev/eng-plan.jpg" class="engin_img_1">
		<div class="engin_b1">Конструктор загородного дома</div>
	</a>
</div>
<? // eng-plan ?>



<? // ссылки ?>
<div class="b_link">
<?
$sql = "SELECT p.url AS url, v.title AS title
FROM content_videos v
INNER JOIN page AS p on p.id_url = v.id_url 
ORDER BY RAND() LIMIT 6";
$r = $db->query($sql);
$link = $r->fetchAll(PDO::FETCH_ASSOC);

foreach ($link as $p) {?>
<a href="<?=$p['url']?>" class="b_link_a">
<img src="/img/link<?=$p['url']?>.jpg">
<div class="b_link_t"><?=$p['title']?></div>
</a>
<?}?>
</div>
<? // ссылки ?>




<? // статьи (проверяем, есть ли статьи)?>
<?
$sql = "SELECT id_cont, h2, content FROM content_videos_st WHERE id_url_cont={$page['id_url']}";
$r = $db->query($sql);
$link = $r->fetchAll(PDO::FETCH_ASSOC);

if(!empty($link[0]['h2'])){ $nm_com = 'class="b_comment_1"'; }
else { $nm_com = 'class="b_comment_2"'; }
?>
<? // статьи ?>




<? // стиль ?>
<div <?=$nm_com?>>
<? if(!empty($link[0]['h2'])){ foreach ($link as $p) {?><div id_cont_text="<?=$p['id_cont']?>" class="bl_staty_t"><?=$p['content']?></div><?}} // статьи ?>
<? require_once ($_SERVER['DOCUMENT_ROOT']."/page/components/comment/comment.php"); // комменты ?>
</div>
<? // стиль ?>



<? // статьи (тайтлы)?>
<?
if(!empty($link[0]['h2'])){ ?>
<div class="b_staty">
<div class="h2_staty">Статьи</div>
<div class="otstup1"></div>
<? foreach ($link as $p) {?><div id_cont_h2="<?=$p['id_cont']?>" class="bl_staty"><?=$p['h2']?></div><?}?>
</div>
<div class="clear"></div>
<?}?>
<? // статьи ?>

<div class="otstup6"></div>



</div>
