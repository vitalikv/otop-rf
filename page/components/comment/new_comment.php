<?php
require_once ($_SERVER['DOCUMENT_ROOT']."/include/bd.php");

$admin = $_POST['admin'];
$id_url = $_POST['id_url'];
$pr_comm = $_POST['pr_comm'];
$answer_comm = $_POST['answer_comm'];
$name = $_POST['name'];
$text = $_POST['text'];

$date = date("Y-m-d-G-i");


$name = preg_replace('|<br>|iU',' ',$name);
if (!preg_match("|[а-яa-z0-9]+|i", $name)) { $name = '';}
if (!preg_match("|[а-яa-z0-9]+|i", $text)) { $text = '';}

$name = work_text($name);
$text = work_text($text);

function work_text($text){
$text = trim($text);
// если текст вставлен из Word, то удаляются все теги
if (preg_match('/class="?Mso|style="[^"]*\bmso-|style=\'[^\'\']*\bmso-|w:WordDocument/', $text)) {
$text = preg_replace('|<br>|isU','[br]',$text);
$text = preg_replace('|<\/p>|isU','[br]',$text);
$text = preg_replace('|\n|isU',' ',$text);
$text = preg_replace('|<!--[\s\S]+-->|isU','',$text);
$text = preg_replace('|<.+>|isU','',$text);
$text = preg_replace('|\[br\]|isU','<br>',$text);
}
// ---
$text = preg_replace('|<br>|isU','[br]',$text);
$text = preg_replace('|<.+>|isU','',$text);
$text = preg_replace('|\[br\]|isU','<br>',$text);
$text = preg_replace('|&nbsp;|i', '', $text); 
$text = preg_replace('|\s{2,}|isU','',$text);
return $text;
}

if($admin=='yes'){
if (preg_match("|admin|i", $name)) { $name = 'Admin';}
}
else {
if (preg_match("|admin|i", $name)) { $name = 'Аноним';} 
}



if (empty($name)){ exit;}
if (empty($text)){ exit;}

$sql = "INSERT INTO comment (id_url, pr_comm, answer_comm, name, comment, date) VALUES ( :id_url, :pr_comm, :answer_comm, :name, :text, :date )";
$r = $db->prepare($sql);
$r->bindValue(':id_url', $id_url);
$r->bindValue(':pr_comm', $pr_comm);
$r->bindValue(':answer_comm', $answer_comm);
$r->bindValue(':name', $name);
$r->bindValue(':text', $text);
$r->bindValue(':date', $date);
$r->execute();

$last_id = $db->lastInsertId();

?>




<?
if($pr_comm==0){ $div_cl = 'comm_text_1'; $nm_pr = $last_id; }
else { $div_cl = 'comm_text_2'; $nm_pr = $pr_comm; }

if($name=='Admin'){ 
if($div_cl=='comm_text_1'){ $div_cl = 'comm_admin_1'; }
if($div_cl=='comm_text_2'){ $div_cl = 'comm_admin_2'; }
}
?>
<div comm_id="<?=$last_id?>" comm_gr="<?=$nm_pr?>" class="<?=$div_cl?>">
<div class="comm_t_name"><?=$name?></div> 
<div class="comm_t_answer"></div> 
<div class="comm_t_date">только что</div><br>
<div class="comm_t_cont"><?=$text?></div>
<div comm_ans="<?=$nm_pr?>" attr_pr="<?=$last_id?>" class="comm_ans">Ответить</div>
</div>







