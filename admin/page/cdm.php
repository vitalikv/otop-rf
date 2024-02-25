


<?
// создаем столбец (img) в таблице (content_page)
$sql = "SELECT img FROM content_page WHERE 0";
$res = $db->query($sql);

if (!$res) {
$sql = "ALTER TABLE content_page ADD img VARCHAR(255) not null";
$stmt = $db->prepare($sql);
$stmt->execute();
}
else { echo 'img'; }
// создаем столбец (img) в таблице (content_page)




// ---------- 
$sql = "SELECT * FROM content_staty WHERE 0"; // проверяем существует ли таблица
$res = $db->query($sql);
if ($res) { 

$sql = "SELECT * FROM content_staty"; // копируем данные
$r = $db->query($sql);
$st = $r->fetchAll(PDO::FETCH_ASSOC);

foreach ($st as $text) {
$sql = "SELECT id_url FROM content_page WHERE id_url={$text['id_url']}";  // проверяем записана ли уже статья
$pg = $db->query($sql);
$y_pg = $pg->fetch(PDO::FETCH_ASSOC);

if(count($y_pg['id_url'])=='0'){  // если статьи нет, то записываем
$sql = "INSERT INTO content_page (id_url, title, h1, descr, content, img) VALUES ( :id_url, :title, :h1, :descr, :content, :img)";
$r = $db->prepare($sql);
$r->bindValue(':id_url', $text['id_url']);
$r->bindValue(':title', $text['title']);
$r->bindValue(':h1', $text['h1']);
$r->bindValue(':descr', $text['descr']);
$r->bindValue(':content', $text['content']);
$r->bindValue(':img', $text['img']);
$r->execute();

echo $text['title'].'<br>';
}
else { 
// $db->exec("DELETE FROM content_page WHERE id_url={$text['id_url']}");
echo 'уже есть<br>'; 
}
}
// вставляем статьи
}
else { echo '<br> таблица не существует'; }
// ----------




// ----------
$sql = "SELECT * FROM content_staty WHERE 0"; // проверяем существует ли таблица
$res = $db->query($sql);
if ($res) { 

$sql = "SELECT * FROM content_razdel"; // копируем данные
$r = $db->query($sql);
$st = $r->fetchAll(PDO::FETCH_ASSOC);

foreach ($st as $text) {
$sql = "SELECT id_url FROM content_page WHERE id_url={$text['id_url']}";  // проверяем записана ли уже статья
$pg = $db->query($sql);
$y_pg = $pg->fetch(PDO::FETCH_ASSOC);

if(count($y_pg['id_url'])=='0'){  // если статьи нет, то записываем
$sql = "INSERT INTO content_page (id_url, title, h1, descr, content) VALUES ( :id_url, :title, :h1, :descr, :content)";
$r = $db->prepare($sql);
$r->bindValue(':id_url', $text['id_url']);
$r->bindValue(':title', $text['title']);
$r->bindValue(':h1', $text['h1']);
$r->bindValue(':descr', $text['descr']);
$r->bindValue(':content', $text['content']);
$r->execute();

echo $text['title'].'<br>';
}
else { 
// $db->exec("DELETE FROM content_razdele WHERE id_url={$text['id_url']}");
echo 'уже есть<br>'; 
}
}
// вставляем разделы
}
else { echo '<br> таблица не существует'; }
// ----------

    
?>

