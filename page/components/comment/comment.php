
<?
require_once ($_SERVER['DOCUMENT_ROOT']."/page/components/comment/russian_date.php");
?>


<? // параметры ?>
<? 
$form_comm_1 = 'Оставьте свой комментарий';
$form_comm_2 = 'Ваше имя';

$word_t1 = '[add_comm="1"]';
$word_n1 = '[add_comm_name="1"]';
?>
<? // параметры ?>


<script>
$(document).ready(function(){

$('[add_comm="1"]').keyup(function () { var word_t1 = '[add_comm="1"]'; ms_word(word_t1); });
$('[add_comm_name="1"]').keyup(function () { var word_t1 = '[add_comm_name="1"]'; ms_word(word_t1); });

$('[add_comm="2"]').keyup(function () { var word_t1 = '[add_comm="2"]'; ms_word(word_t1); });
$('[add_comm_name="2"]').keyup(function () { var word_t1 = '[add_comm_name="2"]'; ms_word(word_t1); });

function ms_word(word_t1){
var text = $(word_t1).html();
if (/class="?Mso|style="[^"]*\bmso-|style='[^'']*\bmso-|w:WordDocument/i.test(text)) {
text = text.replace(/<\/p>/gi, '[br]');
text = text.replace(/<!--[\s\S]+?-->/gi, '');
text = text.replace(/&nbsp;/gi, '');
text = text.replace(/\n/gi, ' ');
text = text.replace(/<[\s\S]+?>/gi, '');
text = text.replace(/\[br\]/gi, '<br>');
$(word_t1).html(text);
}
};


<?// сохраняем новый коммент или подкоммент?>
$(document).on('click', '[but_comm]', function () {

var id = $(this).attr("but_comm");

var id_url = "<?=$page['id_url']?>";
var pr_comm = $(this).attr("pr_comm");
var answer_comm = $(this).attr("answer_comm");
var name = $('[add_comm_name="'+id+'"]').html();
var text = $('[add_comm="'+id+'"]').html();

if(text=="<?=$form_comm_1?>" || name=="<?=$form_comm_2?>"){}
else {
$.ajax({
type: "POST",					
url: '/page/components/comment/new_comment.php',
data: {"admin":"<?=$ad_enter?>", "id_url":id_url, "pr_comm":pr_comm, "answer_comm":answer_comm, "name":name, "text":text},
beforeSend: function(){ $("#loader").css({"display":"block"}); },
success: function(data){ $("#loader").css({"display":"none"});

$('[add_comm="'+id+'"]').html('<?=$form_comm_1?>');
$('[add_comm_name="'+id+'"]').html('<?=$form_comm_2?>');

if(id=='1'){
$('[osn_comm]').after(data);  
$('[add_comm="'+id+'"]').css({"min-height":"18px"});
$('[but_comm="'+id+'"]').css({"display":"none"});
$('[comm_bord_name]').css({"display":"none"});
}
if(id=='2'){
$('[comm_gr="'+pr_comm+'"]:last').after(data);
$('[bl_comm_2]').css({"display":"none"}); 
}
}
});
}
});



<?// при нажатии на 'Оставьте свой комментарий' всплывает форма ?>
$('[add_comm]').focus(function () {
var id = $(this).attr("add_comm");
var text = $('[add_comm="'+id+'"]').html();
if(text=='<?=$form_comm_1?>'){

$('[add_comm="'+id+'"]').html('');
if(id=='1'){  
$('[add_comm="'+id+'"]').css({"min-height":"70px"});
$('[but_comm="'+id+'"]').css({"display":"block"});
$('[comm_bord_name]').css({"display":"block"});
}

}
});


<?// при нажатии на 'Ваше имя' ?>
$('[add_comm_name]').focus(function () {
var id = $(this).attr("add_comm_name");
var text = $('[add_comm_name="'+id+'"]').html();
if(text=='<?=$form_comm_2?>'){ $('[add_comm_name="'+id+'"]').html(''); }
});


<?// при снятии focus с 'Оставьте свой комментарий' ?>
$('[add_comm]').focusout(function () {
var id = $(this).attr("add_comm");
var text = $('[add_comm="'+id+'"]').html();
if(text==''){ $('[add_comm="'+id+'"]').html('<?=$form_comm_1?>'); }
});


<?// при снятии focus с 'Ваше имя' ?>
$('[add_comm_name]').focusout(function () {
var id = $(this).attr("add_comm_name");
var text = $('[add_comm_name="'+id+'"]').html();
if(text==''){ $('[add_comm_name="'+id+'"]').html('<?=$form_comm_2?>'); }
});



<?// при нажатии на 'ответить' ?>
$(document).on('click', '[comm_ans]', function () { 
var pr = $(this).attr("comm_ans");
$('[but_comm="2"]').attr("pr_comm", pr);
var id = $(this).attr("attr_pr");
$('[but_comm="2"]').attr("answer_comm", id);
$('[comm_id="'+id+'"]').after($('[bl_comm_2]'));

if(pr==id){ $('[bl_comm_2]').removeClass("bl_comm_3").addClass("bl_comm_2"); }
else { $('[bl_comm_2]').removeClass("bl_comm_2").addClass("bl_comm_3"); }

$('[bl_comm_2]').css({"display":"block"});
});


});
</script>


<? if($ad_enter=='yes') { // только для admin ?>
<script>
$(document).ready(function(){


<? // редактор коммента ?>
$(document).on('click', '[comm_upd]', function () {
var res = $(this).text();
var id = $(this).attr('comm_upd');
if(res=='изменить'){
$.ajax({
type: "POST",					
url: '/page/components/comment/upd_comment.php',
data: {"id":id, "flag":"select_data"},
beforeSend: function(){ $("#loader").css({"display":"block"}); },
success: function(data){ $("#loader").css({"display":"none"}); 
$('[comm_t_date="'+id+'"]').text(data);
$('[comm_t_cont="'+id+'"]').attr("contenteditable", "true");
$('[comm_t_date="'+id+'"]').attr("contenteditable", "true");
$('[comm_upd="'+id+'"]').text('сохранить');
}
});
}
else { comm_save(id); }  	
});
<? // редактор коммента ?>


<? // сохранить коммент ?>
function comm_save(id){
var date = $('[comm_t_date="'+id+'"]').text();
var comment = $('[comm_t_cont="'+id+'"]').html();
$.ajax({
type: "POST",					
url: '/page/components/comment/upd_comment.php',
data: {"id":id, "flag":"upd_comm", "date":date, "comment":comment},
beforeSend: function(){ $("#loader").css({"display":"block"}); },
success: function(data){ $("#loader").css({"display":"none"}); 
data = $.parseJSON(data);  
if(data[0] == 'ок'){ $('[comm_t_date="'+id+'"]').html(data[1]); }
$('[comm_t_date="'+id+'"]').after('<div class="comm_t_date">'+data[0]+'</div>');
$('[comm_t_cont="'+id+'"]').removeAttr("contenteditable");
$('[comm_t_date="'+id+'"]').removeAttr("contenteditable");
$('[comm_upd="'+id+'"]').text('изменить'); 
}
}); 	
};
<? // сохранить коммент ?>


<? // delet коммент ?>
$('[comm_delet]').click(function () { 
var id = $(this).attr('comm_delet');
$.ajax({
type: "POST",					
url: '/page/components/comment/comm_delet.php',
data: {"id":id},
beforeSend: function(){ $("#loader").css({"display":"block"}); },
success: function(data){ $("#loader").css({"display":"none"}); 
$('[comm_id="'+id+'"]').remove();
}
});
});
<? // delet коммент ?>


});
</script>
<?}?>



<? // комменты ?>
<?
// $sql = "SELECT * FROM comment WHERE id_url = {$page['id_url']} ORDER BY id DESC";

$sql = "SELECT c1.*, c2.name AS name2 
FROM comment c1
LEFT JOIN comment AS c2 on c2.id = c1.answer_comm
WHERE c1.id_url = {$page['id_url']} 
ORDER BY id DESC";

$cc = $db->query($sql);
$comms = $cc->fetchAll(PDO::FETCH_ASSOC);


$sum_comm = count($comms);
?>
<? // комменты ?>


<div class="block_comment_title">Комментарии (<?=$sum_comm?>)</div>


<? // блок ввода нового комментария  ?>
<div class="bl_comm_1">
<div class="comm_bord_html">
<div add_comm="1" class="comm_html" contenteditable="true" spellcheck="true"><?=$form_comm_1?></div>
</div>

<div but_comm="1" pr_comm="0" answer_comm="0" class="but_comm">Комментировать</div>

<div comm_bord_name="" class="comm_bord_name">
<div add_comm_name="1" class="add_comm_name" contenteditable="true" spellcheck="true"><?=$form_comm_2?></div>
</div>
<div class="clear"></div>
</div>
<? // блок ввода нового комментария  ?>


<div osn_comm=""></div> <? // этот элемент нужен чтобы вывести первый комментарий  ?>



<? // дерево комментариев ?>
<?
$res = get_products($comms);
$res = map_tree($res);


// создаем массивы по номерам id
function get_products($res){
$arr_cat = array();
for ($x=0; $x<count($res); $x++) { $arr_cat[$res[$x]['id']] = $res[$x]; } 
return $arr_cat;
}

// создаем дерево комментариев 
function map_tree($dataset) {
$tree = array();
foreach ($dataset as $id=>&$node) {    
if (!$node['pr_comm']){ $tree[$id] = &$node;}
else{ $dataset[$node['pr_comm']]['childs'][$id] = &$node;}
}
return $tree;
}
?>
<? // дерево комментариев ?>




<?
show_comm($res,$ad_enter);

function show_comm($res,$ad_enter){

foreach ($res as $comm) {
if($comm['pr_comm']==0){ $div_cl = 'comm_text_1'; $nm_pr = $comm['id']; }
else { $div_cl = 'comm_text_2'; $nm_pr = $comm['pr_comm']; }

if($comm['name']=='Admin'){ 
if($div_cl=='comm_text_1'){ $div_cl = 'comm_admin_1'; }
if($div_cl=='comm_text_2'){ $div_cl = 'comm_admin_2'; }
}

if($comm['name2']!=''){ $otvet = '» '.$comm['name2']; }
else { $otvet = ''; } 
?>

<div comm_id="<?=$comm['id']?>" comm_gr="<?=$nm_pr?>" class="<?=$div_cl?>">
<div class="comm_t_name"><?=$comm['name']?></div>
<div class="comm_t_answer"><?=$otvet?></div> 
<div comm_t_date="<?=$comm['id']?>" class="comm_t_date"><?russian_date_comm($comm['date'])?></div>

<?if($ad_enter=='yes'){ // кнопка редактирования (только для admin) ?>
<div comm_upd="<?=$comm['id']?>" class="comm_upd">изменить</div> 
<?}?>

<br>
<div comm_t_cont="<?=$comm['id']?>" class="comm_t_cont"><?=$comm['comment']?></div>
<div comm_ans="<?=$nm_pr?>" attr_pr="<?=$comm['id']?>" class="comm_ans">Ответить</div>

<?if($ad_enter=='yes'){ // удаление коммента (только для admin) ?>
<div comm_delet="<?=$comm['id']?>" class="comm_delet"></div>
<?}?>
</div>
<?
if ($comm['childs']){ asort($comm['childs']); show_comm($comm['childs'],$ad_enter); } // если есть подкоментарии, то выводим их 
}}?>







<? // блок ввода подкомментария  ?>
<div bl_comm_2="" class="bl_comm_2">
<div class="comm_bord_html">
<div add_comm="2" class="comm_html_2" contenteditable="true" spellcheck="true"><?=$form_comm_1?></div>
</div>

<div but_comm="2" pr_comm="" answer_comm="" class="but_comm_2">Комментировать</div>

<div class="comm_bord_name_2">
<div add_comm_name="2" class="add_comm_name" contenteditable="true" spellcheck="true"><?=$form_comm_2?></div>
</div>
<div class="clear"></div>
</div>
<? // блок ввода подкомментария  ?>


