<link rel="stylesheet" href="/css/css_comment.css">

<script>
$(document).ready(function(){			


<? // добавляем attr к img ?>
$('.infotext img').each(function() { 
var img_name = $(this).attr('src');

var img = document.createElement('img');
img.src = img_name;
img.onload = function () { 
if(img.width>550 | img.height>500){ 
// $('[src="'+img_name+'"]').css({"border":"1px solid #ff0000","cursor":"pointer"});
$('[src="'+img_name+'"]').attr("class", "newimg")
$('[src="'+img_name+'"]').attr("click_img", "");
};
};
});
<? // добавляем attr к img ?>


<? // img ?>
$(document).on('click', '[click_img]', function () { 
var img = $(this).attr('src');
$('[fon]').html('<img src="'+img+'" class="img_big_2">');
$(".img_big_2").bind("load",function(){ 
$('[fon]').css({"display":"block"}); 
var h_html = $(this).height();
var h_okno = $(window).height();
var h_resul = (h_okno-h_html)/2;
$(this).css("margin-top", h_resul);
});
});		
<? // img ?>


<? // закрытие img ?>
$(document).on('click', '.img_big_2', function () { return false; });
$(document).on('click', '[fon]', function () { $('[fon]').css({"display":"none"}); });
<? // закрытие img ?>

		
});
</script>

<div class="fon" fon=""></div> <? // фон под big img ?>


<div class="infotext">

<h1><?=$cont['h1']?></h1>

<? 
preg_match('|<div class="v_youtube">(.*)</iframe></div>|Usi', $cont['content'], $arr); $clip = $arr[0]; 
$cont['content'] = preg_replace('|<div class="v_youtube">(.*)</iframe></div>|iU','',$cont['content']);
?>
<?=$clip?>

<? if(1==2){ ?>
<!-- otop_p_top_2 -->
<div class="ads_staty_top">
<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-5657772487610973" data-ad-slot="9912228641" data-ad-format="auto"></ins>
<script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
</div>
<!-- otop_p_top_2 -->
<? } ?>


<div class="v_opis_2">
<?=$cont['content']?>
</div>


<div class="v_opis_3">

<!-- последние статьи -->
<? 
$sql = "SELECT p.url AS url, c.title AS title, c.img AS img
FROM content_page c
INNER JOIN page AS p on p.id_url = c.id_url 
WHERE shablon = 'staty'
ORDER BY RAND() LIMIT 10";
$r = $db->query($sql);
$st = $r->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="right_b_1">
<div class="right_b_1t">Статьи</div>
<?foreach ($st as $text) {?>
<div class="videos_st">
<?if($text['img']!=''){?><div class="videos_st_b"><img src="<?=$text['img']?>"></div><?}?>
<div class="videos_st_b"><a href="<?=$text['url']?>" class="ind_links"><?=$text['title']?></a></div>
</div>
<?}?>
</div>
<!-- последние статьи -->
</div>

<div class="clear"></div>

<div class="line_vd"></div>


<!-- otop_videos_362x300 -->
<div class="ads_370x320">
<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<ins class="adsbygoogle" style="display:inline-block;width:362px;height:300px" data-ad-client="ca-pub-5657772487610973" data-ad-slot="7606725045"></ins>
<script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
</div>
<!-- otop_videos_362x300 -->



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




<? // комменты ?>
<div class="b_comment_2">
<? require_once ($_SERVER['DOCUMENT_ROOT']."/page/components/comment/comment.php"); ?>
</div>
<? // комменты ?>


<div class="otstup6"></div>



</div>
