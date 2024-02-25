<!DOCTYPE html>
<html lang="en">
<head>

<script src="/admin/js/jquery.js"></script>
<script src="/admin/js/cookie.js"></script>
</head>
<body>

<script>
$(document).ready(function(){ 


$('[butt]').click(function(){ 	 
var val = $('[kod]').val(); 
$.ajax({
type: "POST",					
url: '/admin/views/enter/1.php',
data: {"val":val},
beforeSend: function(){$("#loader").css({"display":"block"});},
success: function(data){$("#loader").css({"display":"none"});
data = $.parseJSON(data);  
if(data[0] == 0){$('[ind]').html(data[1]);}
if(data[0] == 1){$('[ind]').html(data[1]);
$.cookie('pass', data[1], { expires : 360, path: '/' });
window.location.href = "/upr";
}
}
});    
});
<? //--------- ?>

});
</script>

<div style="margin-top:120px; text-align:center; font-size:44px; color:#2861c9;">Вход в редактор статей</div>

<div style="width:430px; margin:50px auto;">
<div style="float:left; width:270px; margin:0 auto;border:1px solid #2861c9; border-radius:3px; background:#fff;">
<input type="text" placeholder="Пароль" kod="" style="width:260px; font-size:19px; margin:5px; border:none;">
</div>
<div butt="" style="float:left; width:100px; font-size:19px; margin:0 5px 0 35px; border-radius:3px; color:#fff; background:#2861c9; text-align:center; padding:6px; cursor:pointer;">Войти</div>
<div style="clear:both; margin:0px;"></div>
</div>

<div ind="" style="margin-top:120px; text-align:center; font-size:24px; color:#ff0000;"></div>

</body>
</html>