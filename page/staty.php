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


<? // содержание и якоря ?>
$('[link_a]').click(function () {  
var num = $(this).attr("link_a");
var dest = $('[link_t="'+num+'"]').offset().top;
$('html, body').animate({ scrollTop: dest - 70 }, 1100);
});
<? // содержание и якоря ?>


<? // ocsg ?>
window.onload = function() {
var dest = $('[ocsg]').offset().top-250;
var h = $('[ocsg]').height();
var dest2 = $('[stop_line]').offset().top;
var n_ocsg = dest2 - dest - h - 160;
var line = dest2 - h - 210;
$(window).scroll(function() {
var pos = $(document).scrollTop();
if (pos > dest & pos < line) { $('[ocsg]').stop().animate({marginTop: pos - dest + 50}, 0); }
else if (pos < dest) { $('[ocsg]').stop().animate({marginTop: 50}, 0); }
else { $('[ocsg]').stop().animate({marginTop: n_ocsg}, 0); }
});
};
<? // ocsg ?>

		
});
</script>

<div class="fon" fon=""></div> <? // фон под big img ?>

<div class="infotext">

<h1><?=$cont['h1']?></h1>


<div class="v_opis_2">

<? // содержание ?>
<div class="sdt">
<div class="sdt_t">Содержание:</div>
<? 
preg_match_all('|<h[2-3]>(.*)</h[2-3]>|Usi', $cont['content'], $arr); $hz = $arr[0];
$hz = preg_replace("|<h2>(.*)</h2>|iU","<div link_a='\$1' class='sdt_h2'>\$1</div>",$hz);
$hz = preg_replace("|<h3>(.*)</h3>|iU","<div link_a='\$1' class='sdt_h3'>\$1</div>",$hz); 
foreach ($hz as $h) { echo $h; } 
$cont['content'] = preg_replace("|<h(\d)>(.*)</h(\d)>|iU","<h\$1 link_t='\$2'>\$2</h\$3>",$cont['content']); 
?>
</div>
<? // содержание ?>


<? if(1==2){ ?>
<!-- otop_p_top_2 -->
<div class="ads_staty_1">
<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-5657772487610973" data-ad-slot="9912228641" data-ad-format="auto"></ins>
<script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
</div>
<!-- otop_p_top_2 -->
<? } ?>


<?=$cont['content']?>
</div>



<div class="v_opis_3">


<? // warm_floor ---- ?>
<div class="right_b_1">
	<a href="/calculator/warm_floor" class="ind_links">
		<div class="right_b_1t">Программа<br>теплый пол</div>

		<div class="schem_ln">
			<img src="/img/staty/prev/10.jpg">
		</div>
	</a>
</div>
<? // warm_floor ---- ?>


<? // eng-plan ?>
<div class="right_b_1">
	<a href="https://engineering-plan.ru/" class="ind_links" target="_blank">
		<div class="right_b_1t">Конструктор<br>загородного дома</div>

		<div class="schem_ln">
			<img src="/img/staty/prev/eng-plan.jpg">
			<div class="ind_links" style="text-align: center;">Проектирование дома</div>
		</div>
	</a>
</div>
<? // eng-plan ?>


<? // схемы ?>
<? 
$sql = "SELECT p.url AS url, c.h1 AS h1, c.img AS img
FROM content_page c
INNER JOIN page AS p on p.id_url = c.id_url 
WHERE shablon = 'schema'
ORDER BY id DESC";
$r = $db->query($sql);
$st = $r->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="right_b_1">
<div class="right_b_1t">Схема</div>
<?foreach ($st as $text) {?>
<div class="schem_ln">
<div class="schem_ln_b"><img src="<?=$text['img']?>"></div>
<div class="schem_ln_b"><a href="<?=$text['url']?>" class="ind_links"><?=$text['h1']?></a></div>
</div>
<?}?>
</div>
<? // схемы ?>


<div class="otstup5"></div>


<? // ссылки ?>
<?
$sql = "SELECT p.url AS url, v.title AS title
FROM content_videos v
INNER JOIN page AS p on p.id_url = v.id_url 
ORDER BY RAND() LIMIT 14";
$r = $db->query($sql);
$link = $r->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="right_b_1">
<div class="right_b_1t">Видео</div>
<?foreach ($link as $text) {?>
<div class="st_vd">
<div class="st_vd_b"><img src="/img/link<?=$text['url']?>.jpg"></div>
<div class="st_vd_b"><a href="<?=$text['url']?>" class="ind_links"><?=$text['title']?></a></div>
</div>
<?}?>
</div>
<? // ссылки ?>


<? if(1==2){ ?>
<a href="http://engineering-plan.ru" ocsg="" class="ocsg" target="_blank">
<img src="/img/engineering/1.png" class="engin_img_1">
<div class="engin_b1">Конструктор отопления из полипропилена</div>
</a>
<? } ?>


</div>
<div class="clear"></div>


<div stop_line="" class="line_vd"></div>

<? // staty_362x300 ?>
<div class="ads_370x320">
<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<ins class="adsbygoogle" style="display:inline-block;width:362px;height:300px" data-ad-client="ca-pub-5657772487610973" data-ad-slot="1591096240"></ins>
<script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
</div>
<? // staty_362x300 ?>


<? // последние статьи ?>
<div class="b_link">
<?
$sql = "SELECT p.url AS url, c.title AS title, c.img AS img
FROM content_page c
INNER JOIN page AS p on p.id_url = c.id_url 
WHERE shablon = 'staty'
ORDER BY RAND() LIMIT 6";
$r = $db->query($sql);
$link = $r->fetchAll(PDO::FETCH_ASSOC);

foreach ($link as $p) {?>
<a href="<?=$p['url']?>" class="b_link_a">
<img src="<?=$p['img']?>">
<div class="b_link_t"><?=$p['title']?></div>
</a>
<?}?>
</div>
<? // последние статьи ?>



<div>
<div class="clear"></div>
</div>


</div>