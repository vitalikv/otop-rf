

<script>
$(document).ready(function(){


<?// при нажатии на 'ответить' ?>
$('[ad_panel]').click(function () { 

var txt = $('[ad_panel]').html();
if(!$.cookie('ad_panel')){ 
$('[ad_panel]').html('Выйти'); 
$.cookie('ad_panel', 'yes', { expires : 360, path: '/' });
window.location.href = "<?$_SERVER['REQUEST_URI']?>"; 
}
else{ 
$('[ad_panel]').html('Войти'); 
$.cookie('ad_panel', null, { expires : -360, path: '/' }); 
window.location.href = "<?$_SERVER['REQUEST_URI']?>";
}

});



});
</script>

<? 
if ($_COOKIE['ad_panel']=='yes') { $zvr = 'Выйти'; }
else { $zvr = 'Войти'; } 
?>

<div class="ad_panel">
<div ad_panel="" class="ad_panel_butt"><?=$zvr?></div>
</div>









