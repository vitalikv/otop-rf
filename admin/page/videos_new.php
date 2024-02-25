<link rel="stylesheet" href="/admin/css/add.css">


<script>
$(document).ready(function(){



<?// сохраняем вопрос ?>
$('[save_quest]').click(function(){  
var url = $('[url_quest]').html();
var razdel = $('[select_quest] option:selected').text();	
var title = $('[title_quest]').html();
var h1 = $('[h1_quest]').html();
var url_want = $('[url_want]').html();
var er;
if (/[а-яА-Я]+/.test(title)) { $('[err_1]').html(''); } else { $('[err_1]').html('напишите название статьи'); er = 1;} 
if (/[а-яА-Я]+/.test(h1)) { $('[err_2]').html(''); } else { $('[err_2]').html('напишите заголовок статьи'); er = 1;}  
if ( url_want != 'Свободен' ) { er = 1; } 
if ( razdel == '---' ) { $('[err_3]').html('не задан раздел'); er = 1; } else { $('[err_3]').html(''); }

if (er !== 1) {
$.ajax({
type: "POST",					
url: '/admin/page/components/page/save_new_page.php',
data: {"url":url, "razdel":razdel, "title":title, "h1":h1},
beforeSend: function(){$("#loader").css({"display":"block"});},
success: function(data){
$("#loader").css({"display":"none"});  
$('[inf]').html(data);
$('[url_quest]').html('/');
$('[title_quest]').html('');
$('[title_quest]').html('');
$('[h1_quest]').html('');
$('[select_quest] [sl_st]').prop("selected", true);
}
});
}  
});
<?// сохраняем вопрос ?>


<?// проверка Url ?>
$('[url_quest]').focusout(function () {  
var url = $('[url_quest]').html();
$.ajax({
type: "POST",					
url: '/admin/page/components/page/new_page/url_test.php',
data: {"url":url},
beforeSend: function(){$("#loader").css({"display":"block"});},
success: function(data){
$("#loader").css({"display":"none"});  
$('[url_want]').html(data);
data = $.parseJSON(data);  
if(data[0] == 'Свободен'){$('[url_want]').html(data[0]); $('[url_want]').css({"color":"#29ad3b"});} 
if(data[0] == 'Занят'){$('[url_want]').html(data[0]); $('[url_want]').css({"color":"#ff0000"});} 
}
});
});
<?// проверка Url ?>

});
</script>



<div class="bl_left_quest">
<div class="zag_quest_1">Url</div>
<div url_quest="" class="url_quest" contenteditable="true" spellcheck="true">/</div>
<div url_want="" style="display:inline-block; margin:10px 15px 0px 15px; font-size:12px;"></div>

<select select_quest="" class="select_quest">
<option selected sl_st="">---</option>
<option>videos</option>
<option>razdel</option>
</select>

<br><br>
<div class="zag_quest_1">Название статьи title</div>
<div title_quest="" class="title_quest" contenteditable="true" spellcheck="true"></div>
<br>
<div class="zag_quest_1">Заголовок статьи h1</div>
<div h1_quest="" class="title_quest" contenteditable="true" spellcheck="true"></div>


<div err_3="" class="erro"></div>
<div err_1="" class="erro"></div>
<div err_2="" class="erro"></div>
<div inf="" class="infor"></div>
<div class="butt_enter" save_quest="">Создать страницу</div>

</div>

<div class="clear"></div>



