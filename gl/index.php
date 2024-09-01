<? require_once("include/bd.php");  ?>
<?php $vrs = '=25' ?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title><?=$title?></title>
	<meta name="description" content="<?=$description?>" />
	<link rel="stylesheet" href="<?=$path?>css/style.css?<?=$vrs?>"> 
	<link rel="stylesheet" href="<?=$path?>css/toggle.css?<?=$vrs?>">
</head>

<? require_once("../include/metrikaYa.php"); ?>

<body>
	<script>
		var vr = "<?=$vrs ?>";
		
		var infProject = JSON.parse('<?=$jsonPhp?>');

		console.log('version '+ vr);		
	</script>
	
			
	
    <script src="<?=$path?>js/three.min.js"></script>
    <script src="<?=$path?>js/jquery.js"></script>
    <script src="<?=$path?>js/ThreeCSG.js"></script>       
	<script src="<?=$path?>js/OBJLoader.js"></script>
	<script src="<?=$path?>js/MTLLoader.js"></script>   
	
	<script src="<?=$path?>js/loader/inflate.min.js"></script>
	<script src="<?=$path?>js/loader/FBXLoader.js"></script>
	
	
	<? require_once("include/top_1.php"); ?>
	
	<noindex>		 
	<? require_once("include/left_panel_1.php"); ?>	
	<div class="right_panel_1" style="z-index: 1;"></div>
	<? require_once("include/bottom_panel_1.php"); ?>	
	<? require_once("include/modal_window_1.php"); ?>
	<? require_once("include/modal_window_3.php"); ?>
		
	
	<div class="help" style=" z-index: 1;">
		<a href="https://rutube.ru/video/c0dfef93a75bb585db6d8533d81fa99c/" class="button_youtube button_gradient_1" data-action ='top_panel_1' target="_blank">
			<div>видеоинструкция</div>
		</a>
	</div>	
	</noindex>
	
	
	<script src="<?=$path?>tw5.js?<?=$vrs?>"></script>	

</body>

</html>