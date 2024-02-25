

<script>
$(document).ready(function(){ 


<? // выводим редактор ?>
vivod();
function vivod(){  
$.ajax({
type: "POST",					
url: '/admin/page/components/redaktor/views.php',
data: {"id":"new", "razdel":"<?=$met_1?>", "opisan_1":"<?=$opisan_1?>", "opisan_2":"<?=$opisan_2?>"},
beforeSend: function(){ $("#loader").css({"display":"block"}); },
success: function(data){ $("#loader").css({"display":"none"}); $('[wysiwyg]').html(data); }
});    
};
<? // --------- ?>

			
});
</script>


<div wysiwyg=""></div>
