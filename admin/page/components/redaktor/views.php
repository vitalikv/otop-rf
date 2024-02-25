<link rel="stylesheet" href="/admin/css/staty.css?3">
<script src="/admin/js/ajaxupload.js"></script>

<?
$id = $_POST['id'];
$razdel = $_POST['razdel'];
$opisan_1 = $_POST['opisan_1'];
$opisan_2 = $_POST['opisan_2'];
?>

<?
require_once ($_SERVER['DOCUMENT_ROOT']."/include/bd.php");

if($id != 'new'){  // если это редактирование статьи, то вставляем статью
$sql = "SELECT p.url AS url, s.title AS title, s.h1 AS h1, s.descr AS descr, s.content AS content, s.img AS img
FROM content_page s
INNER JOIN page AS p on p.id_url = s.id_url 
WHERE s.id = {$id} 
LIMIT 1";

$r = $db->prepare($sql);
$r->bindValue(':id', $id);
$r->execute();
$res = $r->fetch(PDO::FETCH_ASSOC);


// вытаскиваем все img из текста
preg_match_all('|<img src="(.*)"|Usi', $res['content'], $arr); $arr = $arr[1];
foreach ($arr as $a) { $img .= '<f>'.$a.'</f>'; }
}

?>

<script>
$(document).ready(function(){

<? // важная деталь, запоминает место курсора ?>
var savedRange; 
$('[st_text]').click(function(){ savedRange = window.getSelection().getRangeAt(0); }); 
$('[st_text]').keyup(function(){ savedRange = window.getSelection().getRangeAt(0); });
<? // --- ?>


<? // если текст вставлен из Word, то удаляются все теги  ?>					
$('[st_title]').blur(function(){ ms_word('[st_title]'); });
$('[st_h1]').blur(function(){ ms_word('[st_h1]'); });
$('[st_descr]').blur(function(){ ms_word('[st_descr]'); });
$('[st_text]').blur(function(){ ms_word('[st_text]'); });

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
<? // --- ?>




<?// проверка Url ?>
$('[url]').focusout(function () { var url = $('[url]').html(); var flag = '1'; url_test(url,flag); });
$('[st_title]').focusout(function () { var url = $('[st_title]').html(); var flag = '2'; url_test(url,flag); });

function url_test(url,flag){ 
if(('<?=$razdel?>'!='page' || flag != '2')&&('<?=$id?>'=='new')){
$.ajax({
type: "POST",					
url: '/admin/page/components/redaktor/url_test.php',
data: {'flag':flag, 'url':url},
beforeSend: function(){$("#loader").css({"display":"block"});},
success: function(data){
$("#loader").css({"display":"none"});  
data = $.parseJSON(data);  
if(data[0] == 'Свободен'){$('[url]').html(data[1]); $('[url_want]').html(data[0]); $('[url_want]').css({"color":"#29ad3b"});} 
if(data[0] == 'Занят'){$('[url]').html(data[1]); $('[url_want]').html(data[0]); $('[url_want]').css({"color":"#ff0000"});} 
}
});
}   
};
<?// проверка Url ?>



<?// сохраняем вопрос ?>
$('[save]').click(function(){
var url = $('[url]').html(); 
var razdel = $('[razdel] option:selected').text();
var id_url = $('[id_url]').html();
var title = $('[st_title]').html();
var h1 = $('[st_h1]').html();
var descr = $('[st_descr]').html();
var text = $('[st_text]').html();
var img = $('[img_prev] img').attr("src");
var er;
$('[err_1]').html('');
if(razdel==''){ $('[err_1]').append('выберите раздел <br>'); er = 1; }
if('<?=$id?>'=='new'){ if($('[url_want]').html() != 'Свободен'){ $('[err_1]').append('неверный Url <br>'); er = 1; } }
if (!(/[а-яА-Я]+/.test(title))) { $('[err_1]').append('напишите название статьи <br>'); er = 1; } 
if (!(/[а-яА-Я]+/.test(h1))) { $('[err_1]').append('напишите заголовок статьи <br>'); er = 1; }
// if (!(/[а-яА-Я]+/.test(text))) { $('[err_1]').append('напишите текст <br>'); er = 1; }
if (er !== 1) {
$.ajax({
type: "POST",					
url: '/admin/page/components/redaktor/save.php',
data: {"url":url, "razdel":razdel, "id_url":id_url, "title":title, "h1":h1, "descr":descr, "text":text, "img":img},
beforeSend: function(){$("#loader").css({"display":"block"});},
success: function(data){
$("#loader").css({"display":"none"});  
$('[inf_url]').html(data);
// $('[inf]').css({"display":"block"});
// $('[title_quest]').html('');
// $('[st_text]').html('');
// $('[save_quest]').css({"display":"none"});
delet_img_redaktor();  
delet_img_prev();
}
});
}  
});
<? // --- ?>



<? // удаляет лишние img из папки не сохраненые в редакторе ?>
function delet_img_redaktor(){	
var text = $('[st_text]').html();
var img_old = $('[img_old]').html();
var img_list = $('[img_list]').html();
$.ajax({
type: "POST",					
url: '/admin/page/components/img/delete_img.php',
data: {"text":text, "img_old":img_old, "img_list":img_list},
beforeSend: function(){ },
success: function(data){ }
});
}
<? // --- ?>


<? // удаляет лишние img prev ?>
function delet_img_prev(){	
var img = $('[img_prev] img').attr("src");
var full_img = $('[img_list_prev]').html();
$.ajax({
type: "POST",					
url: '/admin/page/components/img/delete_img_prev.php',
data: {"img":img, "full_img":full_img},
});
}
<? // --- ?>



<? // вставка IMG ?>
new AjaxUpload($('[img_load]'), {
action : '/admin/page/components/img/upload.php',
name : 'uploadfile',						
onSubmit : function(file, ext) { $("#loader").css({"display":"block"}); },
onComplete : function(file, response) { 
$("#loader").css({"display":"none"});
data = $.parseJSON(response); 
if(data[0] == 1){alert(data[1]); } 
if(data[0] == 0){ 
var range = savedRange;
// var htmlNode = document.createElement('img');      это просто вставка img
// htmlNode.setAttribute('src', '/img/staty/'+data[1]);
// range.deleteContents();
// range.insertNode(htmlNode);
var htmlNode = document.createElement('div');
var you_class = 'sp_4'; 
htmlNode.className = you_class;
htmlNode.innerHTML = '<img src="/img/staty/'+data[1]+'">';
range.deleteContents();
range.insertNode(htmlNode); 
$('[img_list]').append('<f>/img/staty/'+data[1]+'</f>');
} 
}							
});
<? // --- ?>


<? // вставка IMG prev ?>
new AjaxUpload($('[img_prev_load]'), {
action : '/admin/page/components/img/prev_upload.php',
name : 'uploadfile',						
onSubmit : function(file, ext) { $("#loader").css({"display":"block"}); },
onComplete : function(file, response) { 
$("#loader").css({"display":"none"});
data = $.parseJSON(response); 
if(data[0] == 1){alert(data[1]); } 
if(data[0] == 0){ 
$('[img_prev]').html('<img src="'+data[1]+'" img_prev_vin="">');
$('[img_list_prev]').append('<f>'+data[1]+'</f>'); 
} 
}							
});
<? // --- ?>



<? // вставка HTML или youtube ?>
$('[ic_html]').click(function(){ 
if( $('[pnl_yuotube]').is(":hidden")) { $('[pnl_yuotube]').css({"display":"block"}); }
else { $('[pnl_yuotube]').css({"display":"none"}); }
});

$('[butt_html]').click(function(){
var youtube = $('[kod_html]').text();
youtube = youtube.replace(/width="[0-9]+"/gi, 'width="100%"');
youtube = youtube.replace(/height="[0-9]+"/gi, 'height="100%"');
var range = savedRange;
var htmlCode = youtube;
var htmlNode = document.createElement('div');
if('<?=$razdel?>'=='page'){ var you_class = 'v_youtube'; }
if('<?=$razdel?>'=='staty'){ var you_class = 'fr_youtube'; }
htmlNode.className = you_class;
htmlNode.innerHTML = htmlCode;
range.deleteContents();
range.insertNode(htmlNode);
$('[kod_html]').html('');
$('[pnl_yuotube]').css({"display":"none"});
});
<? // --- ?>





$('[kn_bold]').click(function(){ document.execCommand('bold', false, null); });
$('[kn_unline]').click(function(){ document.execCommand('underline', false, null); });
$('[kn_h2]').click(function(){ document.execCommand('formatBlock', false, 'h2'); });
$('[kn_h3]').click(function(){ document.execCommand('formatBlock', false, 'h3'); });
$('[kn_list_1]').click(function(){ document.execCommand('insertUnorderedList', false, null); });
$('[kn_list_2]').click(function(){ document.execCommand('insertorderedlist', false, null); });
$('[kn_del]').click(function(){ document.execCommand("removeformat", false, null); });
$('[kn_html]').click(function(){ kn_html(); });
$('[kn_sp_1]').click(function(){ bl_class('sp_1'); });
$('[kn_sp_2]').click(function(){ bl_div('sp_2'); });
$('[kn_sp_3]').click(function(){ bl_div('sp_3'); });
// $('[kn_sp_4]').click(function(){ bl_div('sp_4'); });


function bl_class(name_class){ 
var range = window.getSelection().getRangeAt(0);
var newNode = document.createElement('span'); 
newNode.className = name_class;
if (!range.collapsed) { range.surroundContents(newNode); } <? // если выделн текст, то вставлем тег ?>
}

function bl_div(name_class){ 
var range = window.getSelection().getRangeAt(0);
var newNode = document.createElement('span'); 
newNode.className = name_class;
range.surroundContents(newNode);
}


function kn_html(){
if( $('[st_html]').is(":hidden")) { 
$('[st_text]').css({"display":"none"});
$('[st_html]').css({"display":"block"});
var text = $('[st_text]').html();
$('[st_html]').text(text);
}
else { 
$('[st_html]').css({"display":"none"}); 
$('[st_text]').css({"display":"block"});
var text = $('[st_html]').text();
$('[st_text]').html(text);  
}
};



<? // фиксированное меню ?>
$(window).scroll(function() {
var top = $(document).scrollTop();
if (top > 400) {$('[gr_butt]').attr("class", "fix_gr_butt"); $('[gr_butt_1]').attr("class", "gr_butt_1");}
else {$('[gr_butt]').attr("class", "gr_butt"); $('[gr_butt_1]').attr("class", "");}
});
<? // --- ?>


});
</script>


<div class="gr_razd"><?=$opisan_1?><?=$opisan_2?></div>

<div id_url=""><?=$id?></div>
<div img_old="" style="display:none;"><?=$img?></div>
<div img_list="" style="display:none;"></div>
<div img_list_prev="" style="display:none;"></div>


<?
if($id=='new'){ $tric_1 = 'contenteditable="true" spellcheck="true"'; }
else { $tric_2 = 'disabled'; }
?>

<div class="gr_1">Url</div>
<div url="" class="st_input_3" <?=$tric_1?>><?=$res['url']?></div>
<div url_want="" style="display:inline-block; margin:10px 15px 0px 15px; font-size:12px;"></div>

<? if($razdel=='page' and $id=='new'){ $razdel = ''; } ?>
<select razdel="" class="select_quest" <?=$tric_2?>>
<option selected><?=$razdel?></option>
<option>videos</option>
<option>videos_1</option>
<option>razdel</option>
<option>schema</option>
</select>

<div class="gr_1">
img статьи
<div img_prev="" class="img_prev"><img src="<?=$res['img']?>"></div>
<div img_prev_load="" class="img_prev">загрузить</div>
</div>

<div class="gr_1">title</div>
<div st_title="" class="st_input" contenteditable="true" spellcheck="true"><?=$res['title']?></div>

<div class="gr_1">h1</div>
<div st_h1="" class="st_input" contenteditable="true" spellcheck="true"><?=$res['h1']?></div>

<div class="gr_1">descr</div>
<div st_descr="" class="st_input_2" contenteditable="true" spellcheck="true"><?=$res['descr']?></div>


<div class="gr_2">Текст</div>
<div gr_butt="" class="gr_butt">
<div gr_butt_1="" class="">  
<img kn_bold="" src="/admin/img/textarea/1.png" class='butt'>
<img kn_unline="" src="/admin/img/textarea/3.png" class='butt'>
<img kn_sp_1="" src="/admin/img/textarea/1.png" class='butt'>
<img kn_sp_2="" src="/admin/img/textarea/1.png" class='butt'>
<img kn_sp_3="" src="/admin/img/textarea/18.png" class='butt'>
<img kn_h2="" src="/admin/img/textarea/16.png" class='butt'>
<img kn_h3="" src="/admin/img/textarea/14.png" class='butt'>
<img kn_list_1="" src="/admin/img/textarea/12.gif" class='butt'>
<img kn_list_2="" src="/admin/img/textarea/13.gif" class='butt'>
<img kn_del="" src="/admin/img/textarea/17.png" class='butt'>
<img kn_html="" src="/admin/img/textarea/10.png" class='butt'>
<img ic_html="" src="/admin/img/textarea/15.png" class='butt'>
<img img_load="" src="/admin/img/textarea/8.png" class='butt'>
</div>
</div>

<div st_text="" class="st_text" contenteditable="true" spellcheck="true"><?=$res['content']?></div>
<div st_html="" class="st_html" contenteditable="true" spellcheck="true"></div>

<div inf_url=""></div>

<div err_1=""></div>

<div class="butt_save" save="">Опубликовать</div>



<? // вставка yuotube ?>
<div class="pnl_yuotube" pnl_yuotube="">
<div class="pnl_yuotube_1">
<div kod_html="" class="kod_html" contenteditable="true" spellcheck="true"></div>
<div butt_html="" class="butt_html">Вставить</div>
</div>
</div>
<? // вставка yuotube ?>


