<?  


// разбиваем файл на имя и формат
$pieces = explode("/", $_FILES['uploadfile']['type']);


// проверяем закачиваемый файл иображение
if ($pieces[1]=='jpg' || $pieces[1]=='jpeg' || $pieces[1]=='png' || $pieces[1] == 'gif') {$err = 0;}
else { $err = 1;}


// если это не изображение, то выводим ошибку
if ($err == 1) { $a = $err; $b = 'Вы можете загрузить ТОЛЬКО изображение в формате JPG, GIF или PNG!'; }

// ограничение 1.2 mb 
if ($err == 0) {
$size_img = 1200000; 
$size = filesize($_FILES['uploadfile']['tmp_name']);
if ($size>$size_img){ $err = 1; $a = $err; $b = 'Максимальный размер изображения 1.2 Mb!'; }
}

if ($err == 0) {
// узнаем ширину и высоту изображения
$image_info = getimagesize($_FILES['uploadfile']['tmp_name']);
$img_w = $image_info[0];
$img_h = $image_info[1];

$mnu = date("YmdGis");

// если изображение больше 500px по ширине, то выполняем уменьшение
$image = new SimpleImage();
$image->load($_FILES['uploadfile']['tmp_name']);
if($img_w>=1001){$img_h = $image->resizeToWidth(1000);} 
if($img_h>=1001){$image->resizeToHeight(1000);} 
$image->save($_SERVER['DOCUMENT_ROOT'].'/img/staty/'.$mnu.'.jpg');
   
// выводим изображание 
$a = $err;
$b = $mnu.'.jpg';
}


 
echo json_encode(array($a,$b));


?>

<?


class SimpleImage {

   var $image;
   var $image_type;

   function load($filename) {
      $image_info = getimagesize($filename);
      $this->image_type = $image_info[2];
      if( $this->image_type == IMAGETYPE_JPEG ) {$this->image = imagecreatefromjpeg($filename);}
	  elseif( $this->image_type == IMAGETYPE_GIF ) {$this->image = imagecreatefromgif($filename);} 
	  elseif( $this->image_type == IMAGETYPE_PNG ) {$this->image = imagecreatefrompng($filename);}
   }
   function save($filename, $image_type=IMAGETYPE_JPEG, $compression=75, $permissions=null) {
      if( $image_type == IMAGETYPE_JPEG ) {imagejpeg($this->image,$filename,$compression);} 
	  elseif( $image_type == IMAGETYPE_GIF ) {imagegif($this->image,$filename);} 
	  elseif( $image_type == IMAGETYPE_PNG ) {imagepng($this->image,$filename);}
      if( $permissions != null) {chmod($filename,$permissions);}
   }
   function getWidth() {return imagesx($this->image);}
   function getHeight() {return imagesy($this->image);}
   
   function resizeToWidth($width) {
      $ratio = $width / $this->getWidth();
      $height = $this->getheight() * $ratio;
      $this->resize($width,$height);
	  return $height;
   }
   function resizeToHeight($height) {
      $ratio = $height / $this->getHeight();
      $width = $this->getWidth() * $ratio;
      $this->resize($width, $height);
   }
   
   function resize($width,$height) {
      $new_image = imagecreatetruecolor($width, $height);
      imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
      $this->image = $new_image;
   }
}
?>