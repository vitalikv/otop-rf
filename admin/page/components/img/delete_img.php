<?

$text = $_POST['text']; 
$img_list = $_POST['img_list']; // текст созданной статьи
$img_old = $_POST['img_old']; // список не сохраненных img из updat.php


$img_save = img_save($text);
if (!empty($img_list)){$img = img_list($img_list); delet($img,$img_save); }
if (!empty($img_old)){$img = last_text($img_old); delet($img,$img_save); } 


// удаляем img
function delet($img,$img_save) {
for ($n=0; $n<count($img); $n++) {  
if (!in_array($img[$n], $img_save)) { unlink($_SERVER['DOCUMENT_ROOT'].''.$img[$n]); }
}
}

// новый текст 
function img_save($text) {
preg_match_all('|<img src="(.*)"|Usi', $text, $arr); $arr = $arr[1];
return $arr;
}
// старый текст(до редактирования), вытаскивается список всех img (updat.php)
function last_text($text) {
preg_match_all('|<f>(.*)</f>|Usi', $text, $arr); $arr = $arr[1];
return $arr;
}
// список всех img, которые были добавлены на страницу 
function img_list($text) {
preg_match_all('|<f>(.*)</f>|Usi', $text, $arr); $arr = $arr[1];
return $arr;
}



?>  

