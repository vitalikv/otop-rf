<? require_once("include/bd.php");  ?>
<?php $vrs = '=7' ?>

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

<body>
	<script>
		var vr = "<?=$vrs ?>";
		
		var infProject = JSON.parse('<?=$jsonPhp?>');

		console.log('version '+ vr);		
	</script>
	
			
	
    <script src="<?=$path?>js/three.min.js?<?=$vrs?>"></script>
    <script src="<?=$path?>js/jquery.js"></script>
    <script src="<?=$path?>js/ThreeCSG.js"></script>       
	<script src="<?=$path?>js/OBJLoader.js"></script>
	<script src="<?=$path?>js/MTLLoader.js"></script>   
	
	<script src="<?=$path?>js/loader/inflate.min.js?<?=$vrs?>"></script>
	<script src="<?=$path?>js/loader/FBXLoader.js?<?=$vrs?>"></script>
	
	
	<? require_once("include/top_1.php"); ?>
	<? require_once("include/modal_window_2.php"); ?>
	
	<noindex>		 
	<? require_once("include/left_panel_1.php"); ?>	
	<? require_once("include/right_panel_1.php"); ?>
	<? require_once("include/bottom_panel_1.php"); ?>	
	<? require_once("include/modal_window_1.php"); ?>
	<? require_once("include/modal_window_3.php"); ?>
		
	
	<div class="help" style=" z-index: 1;">
		<a href="https://www.youtube.com/watch?v=rqCZYTKqfIE" class="button_youtube button_gradient_1" data-action ='top_panel_1' target="_blank">
			<img src="<?=$path?>/img/button_youtube.png">
			<div style="padding-left:10px;">видеоинструкция</div>
		</a>
	</div>	
	</noindex>
	
	
	<script src="<?=$path?>tw5.js"></script>	

</body>

<? if($_SERVER['SERVER_NAME']=='xn------6cdcklga3agac0adveeerahel6btn3c.xn--p1ai') {?>	
	<!-- Yandex.Metrika counter --><script type="text/javascript">(function (d, w, c) { (w[c] = w[c] || []).push(function() { try { w.yaCounter15088201 = new Ya.Metrika({id:15088201, enableAll: true, webvisor:true}); } catch(e) {} }); var n = d.getElementsByTagName("script")[0], s = d.createElement("script"), f = function () { n.parentNode.insertBefore(s, n); }; s.type = "text/javascript"; s.async = true; s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js"; if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f); } else { f(); } })(document, window, "yandex_metrika_callbacks");</script><noscript><div><img src="//mc.yandex.ru/watch/15088201" style="position:absolute; left:-9999px;" alt="" /></div></noscript><!-- /Yandex.Metrika counter -->
<?}else{?>
	<script>
	console.log('Stop Metrika', window.location.hostname);
	console.log("<?echo $url?>");
	console.log("<?echo $title?>");
	</script> 
<?}?>

</html>