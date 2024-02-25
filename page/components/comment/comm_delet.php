<?
require_once ($_SERVER['DOCUMENT_ROOT']."/include/bd.php");

$id = $_POST['id'];


$sql = "DELETE FROM comment WHERE id = {$id}";
$r = $db->exec($sql);


?>







