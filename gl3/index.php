<? require_once("include/bd.php");  ?>
<?php $vrs = '=45' ?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title><?=$title?></title>
	<meta name="description" content="<?=$description?>" />
	<link rel="stylesheet" href="<?=$path?>css/style.css?<?=$vrs?>"> 
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
  
	
	<script src="<?=$path?>js/dp/EffectComposer.js?<?=$vrs?>"></script>
	<script src="<?=$path?>js/dp/CopyShader.js?<?=$vrs?>"></script>
	<script src="<?=$path?>js/dp/RenderPass.js?<?=$vrs?>"></script>
	<script src="<?=$path?>js/dp/ShaderPass.js?<?=$vrs?>"></script>
	<script src="<?=$path?>js/dp/OutlinePass.js?<?=$vrs?>"></script>
	

	
	<? if($_SERVER['SERVER_NAME']=='3d-stroyka' && $interface['rtc']) {?> 
	<script src="<?=$path?>js/OBJLoader.js"></script>
	<script src="<?=$path?>js/MTLLoader.js"></script> 	
	<script src="<?=$path?>js/loader/inflate.min.js?<?=$vrs?>"></script>
	<script src="<?=$path?>js/loader/FBXLoader.js?<?=$vrs?>"></script>
	<?}?>	
	
	
	<? if($_SERVER['SERVER_NAME']=='3d-stroyka' && $interface['rtc']) 
	{ 
		require_once("admin/catalog/admin_catalog.php");
		require_once("admin/obj/menu_fbx.php");
	} ?>
	
	<div class="frame" nameId="frameG">
			
		<div class="flex_1 top_panel_1 button_gradient_1" data-action ='top_panel_1'>
			<div class="go_home align_items" nameId="butt_main_menu">
				<div class="go_home_txt">
					Меню
				</div>
			</div>
			<div class="title_1"><h1><?=$h1?></h1></div>			
		</div>	
		
		<noindex>
		
		<div class="flex_1 height100">
			
			<div nameId="msDiv_1" style="display: none; position: absolute; left: 50%; top: 50%; border:solid 1px #b3b3b3; background: #fff; padding: 5px 10px; font-family: arial,sans-serif; font-size: 15px; color: #666;">
				
			</div>
			
			<div nameId="mainDiv_1" style="flex-grow:1; position: relative;">
				<? require_once("include/top_1.php"); ?>			
				
						
				
				<? require_once("include/modal_window_3.php"); ?>
				
				<? if($_SERVER['SERVER_NAME']=='3d-stroyka' && $interface['rtc']) 
				{ 
					//require_once("include/modal_window_2.php");
					require_once("include/bottom_panel_1.php");
					
				} ?>					
				
			
				<div class="help">
					<a href="https://youtu.be/kFdMB4p7gbU" class="button1 button_gradient_1" data-action ='top_panel_1' target="_blank">
						<img src="<?=$path?>/img/button_youtube.png">
						<div style="padding-left:10px;">видеоинструкция</div>
					</a>	
				</div>
				
				
				
				
					<div nameId="block_pos" class="block_pos" ui_1="">
						<div style="display: flex;">
							<div style="display: flex; align-items: center;">
								<div class="button1 button_gradient_1" nameId="select_pivot">
									<img src="<?=$path?>/img/move_1.png">
								</div>	
								
								<div class="flex_1 input_rotate">
									<input type="text" nameId="object_pos_X" value="0">
									<input type="text" nameId="object_pos_Y" value="0">
									<input type="text" nameId="object_pos_Z" value="0">
								</div>	
							</div>
							
							<div style="display: flex; align-items: center; margin-left: 40px;">
								<div class="button1 button_gradient_1" nameId="select_gizmo">
									<img src="<?=$path?>/img/rotate_1.png">	
								</div>	

								<div class="flex_1 input_rotate">
									<div class="flex_1" style="position: relative; margin: 0 5px;">
										<div class="button1 button_gradient_1" nameId="obj_rotate_X_90m" style="position: absolute; left: 0; width: 10px;">-</div>
										<input type="text" nameId="object_rotate_X" value="0">
										<div class="button1 button_gradient_1" nameId="obj_rotate_X_90" style="position: absolute; right: 0; width: 10px;">+</div>
									</div>
									
									<div class="flex_1" style="position: relative; margin: 0 5px;">
										<div class="button1 button_gradient_1" nameId="obj_rotate_Y_90m" style="position: absolute; left: 0; width: 10px;">-</div>
										<input type="text" nameId="object_rotate_Y" value="0">
										<div class="button1 button_gradient_1" nameId="obj_rotate_Y_90" style="position: absolute; right: 0; width: 10px;">+</div>
									</div>

									<div class="flex_1" style="position: relative; margin: 0 5px;">
										<div class="button1 button_gradient_1" nameId="obj_rotate_Z_90m" style="position: absolute; left: 0; width: 10px;">-</div>
										<input type="text" nameId="object_rotate_Z" value="0">
										<div class="button1 button_gradient_1" nameId="obj_rotate_Z_90" style="position: absolute; right: 0; width: 10px;">+</div>
									</div>									
									
								</div>	

								<div class="flex_1">
									<div style="width: 20px; height: 2px; background: rgb(247, 72, 72);"></div>
									<div style="width: 20px; height: 2px; background: rgb(17, 255, 0);"></div>
									<div style="width: 20px; height: 2px; background: rgb(72, 116, 247);"></div>
								</div>
													
							
								<div class="button1 button_gradient_1" nameId="obj_rotate_reset">
									сбросить	
								</div>											
							</div>
							
						</div>
					</div>					
				
				
				
				
				
			</div>

			<div nameId="wrap_sborka_1" style="display: none; width: 280px; position: absolute; background: #f1f1f1; border: 1px solid #ccc;">
				<div nameId="list_sborka_1" style="position: relative;"></div>												
			</div>			
					
			
			
			<? require_once("include/right_panel_1.php"); ?>
			
		</div>
		
		</noindex>
		
	</div>
	
	

	
	<script src="<?=$path?>test.js?<?=$vrs?>"></script> 
	

</body>

	<script>
	console.log('test');
	</script> 

</html>