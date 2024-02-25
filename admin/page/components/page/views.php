

<link rel="stylesheet" href="/admin/css/add.css">


<script>
$(document).ready(function(){

<? // если текст вставлен из Word, то удаляются все теги  ?>					
$('[form_quest]').keyup(function () { ms_word('[form_quest]'); });
$('[form_quest]').click(function () { ms_word('[form_quest]'); });
$('[save_quest]').click(function () { ms_word('[form_quest]'); });

function ms_word(content){
var text = $(content).html();
if (/class="?Mso|style="[^"]*\bmso-|style='[^'']*\bmso-|w:WordDocument/i.test(text)) {
text = text.replace(/<\/p>/gi, '[br]');
text = text.replace(/<!--[\s\S]+?-->/gi, '');
text = text.replace(/&nbsp;/gi, '');
text = text.replace(/\n/gi, ' ');
text = text.replace(/<[\s\S]+?>/gi, '');
text = text.replace(/\[br\]/gi, '<br>');
$(content).html(text);
}
};



<?// сохраняем вопрос ?>
$('[save_quest]').click(function(){ 
var type_s = $('[type_s]').html();	
var url = $('[id_url_quest]').html();
var h1 = $('[h1_quest]').html();
var text = $('[form_quest]').html();
var er;
// if (/[а-яА-Я]+/.test(title)) { $('[err_1]').html(''); } else { $('[err_1]').html('напишите название статьи'); er = 1;} 
// if (/[а-яА-Я]+/.test(h1)) { $('[err_1]').html(''); } else { $('[err_1]').html('напишите заголовок статьи'); er = 1;}  
// if (/[а-яА-Я]+/.test(text)) { $('[err_2]').html(''); } else { $('[err_2]').html('напишите текст'); er = 1; }
if (er !== 1) {
$.ajax({
type: "POST",					
url: '/admin/page/components/page/save.php',
data: {"type_s":type_s, "url":url, "h1":h1, "text":text},
beforeSend: function(){$("#loader").css({"display":"block"});},
success: function(data){
$("#loader").css({"display":"none"});  
$('[inf_url]').html(data);
// $('[title_quest]').html('');
// $('[form_quest]').html('');
// $('[save_quest]').css({"display":"none"});  
}
});
}  
});




<? // фиксированное меню ?>
$(window).scroll(function() {
var top = $(document).scrollTop();
if (top > 250) {$('[gr_butt]').attr("class", "fix_gr_butt"); $('[gr_butt_1]').attr("class", "gr_butt_1");}
else {$('[gr_butt]').attr("class", "gr_butt"); $('[gr_butt_1]').attr("class", "");}
});




});
</script>



<div class="bl_left_quest">
<div type_s="" style="display:inline-block;"></div>  
<div id_url_quest="" style="display:inline-block;"></div> 
<br><br>
<div class="zag_quest_1">Url: <div url_quest="" style="display:inline-block;"></div></div>
<br>
<div class="zag_quest_1">Название page: <div title_quest="" style="display:inline-block;"></div></div>
<br>
<div class="zag_quest_1">Заголовок статьи h2</div>
<div h1_quest="" class="title_quest" contenteditable="true" spellcheck="true"></div>


<?// текст ?>
<div class="zag_quest_2">Текст</div>
<div gr_butt="" class="gr_butt">
<div gr_butt_1="" class="">
<img src="/admin/img/textarea/1.png" onClick="document.execCommand('bold', false, null);" class='butt'>
<img src="/admin/img/textarea/3.png" onClick="document.execCommand('underline', false, null);" class='butt'>
<img src="/admin/img/textarea/11.png" onClick="document.execCommand('formatBlock', false, 'h2');" class='butt'>
</div>
</div>
<div class="clear"></div>
<div form_quest="" class="form_quest" contenteditable="true" spellcheck="true"></div>
<?// текст ?>



<div err_1="" class="erro"></div>
<div err_2="" class="erro"></div>
<div inf="" class="infor">
<div inf_url=""></div>
<div class="butt_enter" save_quest="">Опубликовать</div>

</div>

<div class="clear"></div>



