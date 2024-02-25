

<div class="infotext">

<h1><?=$cont['h1']?></h1>


<!-- статьи -->
<? 
$sql = "SELECT p.url AS url, c.title AS title, c.content AS content, c.img AS img
FROM content_page c
INNER JOIN page AS p on p.id_url = c.id_url 
WHERE shablon = 'staty'
ORDER BY id DESC";
$r = $db->query($sql);
$st = $r->fetchAll(PDO::FETCH_ASSOC);
?>
<?foreach ($st as $text) {?>
<div class="st_list_r">

<?if($text['img']!=''){?>
<div class="st_list_b">
<img src="<?=$text['img']?>">
</div>
<?}?>

<div class="st_list_b">
<a href="<?=$text['url']?>" class="st_list_t"><?=$text['title']?></a>
<?
$text = strip_tags($text['content']);
$text = substr($text, 0, 550);
$text = rtrim($text, "!,.-");
$text = substr($text, 0, strrpos($text, ' '));
$text = $text." …";
?>
<div class="st_list_text"><?=$text?></div>
</div>

</div>
<?}?>
<!-- статьи -->


</div>
