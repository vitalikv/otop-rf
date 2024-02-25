

<style>

.window_main_load_obj  
{
	position: relative;
	margin: auto;
	width: 500px;	
	
	background: white;
	border-radius: 8px;
	box-shadow: 0 4px 10px 0 rgba(0, 0, 0, 0.5);
	display: -webkit-box;
	display: flex;
	-webkit-box-orient: vertical;
	-webkit-box-direction: normal;
	flex-direction: column;	
	
	z-index: 2;
}


.window_main_load_obj input
{
	display: block;
	margin: auto;
	margin-bottom: 10px;
	width: 99%;
		
	font-size: 14px;
	text-align: center;
	color: #666;
	
	text-decoration: none;
	line-height: 2em;
	padding: 0;
	
	border: 1px solid #ccc;
	border-radius: 3px;
	background-color:#fff;	
}


.window_main_load_obj .modal_window_close
{
	position: absolute;
	width: 20px;	
	height: 20px;
	top: 10px;
	right: 10px;		

	font-size: 30px;
	line-height: 0.6em;	
	z-index: 100;
}

</style>


<div class="window_main_load_obj" nameId="window_main_load_obj" ui_1="" style="display: block;">
	<div class="modal_window_close" nameId="button_close_main_load_obj">
		+
	</div>
	<div style="width: 80%; margin: auto; padding-top: 30px; padding-bottom: 30px;">

		<input name="file" type="file" id="load_obj_1" class="input_load_substrate">
		<input name="file" type="file" id="load_house_1" class="input_load_substrate">
		
		<label for="load_obj_1" class="button1 button_gradient_1" style="margin: auto;">
			загрузить объект с компьютера
		</label>
		
		<br><br>
		
		<label for="load_house_1" class="button1 button_gradient_1" style="margin: auto;">
			загрузить дом с компьютера
		</label>		

		<div style="font-size: 20px; font-family: arial,sans-serif; color: #666; margin: 10px auto; text-align:center;">
			или
		</div>
		
		<div class="flex_1">		
			<input type="text" nameId="input_link_obj_1" value="">			
		</div>
		
		<div class="button1 button_gradient_1" nameId='butt_load_obj_2'>загрузить по ссылке</div>
	</div>			
</div>