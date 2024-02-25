<link rel="stylesheet" href="/css/contact.css">

<script>
$(document).ready(function(){


<?// сохраняем вопрос ?>
$('[save_quest]').click(function(){  	
var name = $('[mess_name]').html();
var mail = $('[mess_mail]').html();
var text = $('[mess_text]').html();
var er;
if (/[а-яА-Яa-zA-Z]+/.test(name)) { $('[err_1]').html(''); } else { $('[err_1]').html('напишите ваше Имя'); er = 1;} 
if (/^[a-zA-Z0-9-_\.]+@[a-zA-Z0-9-_\.]+\.([a-zA-Z]{2,5})$/.test(mail)) { $('[err_2]').html(''); } else { $('[err_2]').html('напишите email'); er = 1;}  
if (/[а-яА-Яa-zA-Z]+/.test(text)) { $('[err_3]').html(''); } else { $('[err_3]').html('напишите текст сообщения'); er = 1; }
if (er !== 1) {
$.ajax({
type: "POST",					
url: '/page/components/contact.php',
data: {"name":name, "mail":mail, "text":text},
beforeSend: function(){$("#loader").css({"display":"block"});},
success: function(data){
$("#loader").css({"display":"none"}); 

$('[forma]').html(''); 
$('[inf]').html(data);
$('[inf]').css({"display":"block"});
}
});
}  
});



});
</script>

<div class="infotext">


<div class="cotex">
<h1><?=$cont['h1']?></h1>


<? // форма сообщения ?>
<div forma="">
<div class="mess_zag">Ваше Имя</div>
<div mess_name="" class="mess_inp" contenteditable="true" spellcheck="true"></div>
<br>
<div class="mess_zag">Ваш email</div>
<div mess_mail="" class="mess_inp" contenteditable="true" spellcheck="true"></div>

<br><br>

<div class="mess_zag">Сообщение</div>
<div mess_text="" class="mess_text" contenteditable="true" spellcheck="true"></div>

<div err_1="" class="erro"></div>
<div err_2="" class="erro"></div>
<div err_3="" class="erro"></div>

<div class="butt_enter" save_quest="">Отправить</div>
</div>
<? // форма сообщения ?>


<div inf="" class="infor"></div>


</div>

</div>