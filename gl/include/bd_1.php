<?
try
{
	$db = new PDO('mysql:host=localhost;dbname=eng_1', 'root', 'ns62QYhqMf');
	$db->exec("set names utf8");
}
catch(PDOException $e)
{
    echo 'Ошибка 1';
}








