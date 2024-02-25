<?php
require_once ($_SERVER['DOCUMENT_ROOT']."/include/bd.php");

$flag = $_POST['flag'];
$url = $_POST['url'];
$url = preg_replace('|<.*>|iU','',$url);
$url = trim($url);

if($flag == '2'){
$url = translitIt($url);
$url = '/staty/'.$url;
}

// проверяем наличие такого же url 
$sql = "SELECT url FROM page WHERE url = :url";
$r = $db->prepare($sql);
$r->bindValue(':url', $url);
$r->execute();
$set_url = $r->fetch(PDO::FETCH_ASSOC);
if(count($set_url['url'])>0){ $err = 'Занят'; }
else { $err = 'Свободен'; }

echo json_encode(array($err,$url));




function translitIt($str){
    $tr = array(
        "А"=>"a","Б"=>"b","В"=>"v","Г"=>"g","Д"=>"d",
        "Е"=>"e","Ё"=>"yo","Ж"=>"j","З"=>"z","И"=>"i",
        "Й"=>"y","К"=>"k","Л"=>"l","М"=>"m","Н"=>"n",
        "О"=>"o","П"=>"p","Р"=>"r","С"=>"s","Т"=>"t",
        "У"=>"u","Ф"=>"f","Х"=>"h","Ц"=>"c","Ч"=>"ch",
        "Ш"=>"sh","Щ"=>"sch","Ъ"=>"","Ы"=>"yi","Ь"=>"",
        "Э"=>"e","Ю"=>"yu","Я"=>"ya","а"=>"a","б"=>"b",
        "в"=>"v","г"=>"g","д"=>"d","е"=>"e","ё"=>"yo","ж"=>"j",
        "з"=>"z","и"=>"i","й"=>"y","к"=>"k","л"=>"l",
        "м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
        "с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h",
        "ц"=>"c","ч"=>"ch","ш"=>"sh","щ"=>"sch","ъ"=>"y",
        "ы"=>"y","ь"=>"","э"=>"e","ю"=>"yu","я"=>"ya",
        " "=> "_", "."=> "", "/"=> "_"
    );
	$str=strtr($str,$tr);
	$str=strtolower($str);
	$str=preg_replace('|[^a-zA-Z0-9-_]|isU','',$str);
	$str=preg_replace('/^_|_$|^-|-$/isU','',$str);
    return $str;
}
?>





