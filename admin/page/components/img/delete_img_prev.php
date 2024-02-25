<?

$img = $_POST['img']; 
$full_img = $_POST['full_img'];  

preg_match_all('|<f>(.*)</f>|Usi', $full_img, $arr); $full = $arr[1];



// удаляем img
for ($n=0; $n<count($full); $n++) {  
if ($img != $full[$n]) { unlink($_SERVER['DOCUMENT_ROOT'].''.$full[$n]); }
}


?>  

