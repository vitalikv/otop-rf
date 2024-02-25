




document.body.querySelector('#load_obj_1').addEventListener( 'change', readURL_2, false );


function readURL_2(e) 
{
	if (this.files[0]) 
	{		
		var reader = new FileReader();
		reader.onload = function (e) 
		{						
			loadInputFile({data: e.target.result});
		};				

		reader.readAsArrayBuffer(this.files[0]);  									
	};
};

var el_1 = document.body.querySelector('[nameId="butt_main_load_obj"]');
var el_2 = document.body.querySelector('[nameId="button_close_main_load_obj"]');
var el_3 = document.body.querySelector('[nameId="butt_load_obj_2"]');
var el_4 = document.body.querySelector('[nameId="window_main_load_obj"]');
var el_5 = document.body.querySelector('[nameId="input_link_obj_1"]');

el_1.onmousedown = function() { el_4.style.display = 'block'; }
el_2.onmousedown = function() { el_4.style.display = 'none'; }
el_3.onmousedown = function() { loadUrlFile(); }



// загрузка fbx объекта с компа
function loadInputFile(cdm)
{
	var loader = new THREE.FBXLoader();
	var obj = loader.parse( cdm.data );		
	setParamObj({obj: obj});			

	el_4.style.display = 'none';
	
	blockKeyCode({block: false});
}


// загрузка fbx объекта по url
function loadUrlFile()
{	
	var url = el_5.value; 
	var url = url.trim();
	
	// /gl2/import/nasos_1.FBX 
	
	var loader = new THREE.FBXLoader();
	loader.load( url, function ( obj ) 						
	{ 			
		setParamObj({obj: obj});
	});			

	el_4.style.display = 'none';
}



// добавляем объект в сцену
function setParamObj(cdm)
{	
	var obj = cdm.obj;
	
	var obj = getBoundObject_1({obj: obj});
	
	//var obj = obj.children[0];		
	obj.position.set(0, 1, 0); 	

	planeMath.position.y = 1; 
	planeMath.rotation.set(-Math.PI/2, 0, 0);
	planeMath.updateMatrixWorld(); 	
 
	obj.userData.tag = 'obj';
	obj.userData.obj3D = {};
	obj.userData.obj3D.lotid = 0;
	obj.userData.obj3D.nameRus = 'неизвестный объект';
	obj.userData.obj3D.typeGroup = '';
	obj.userData.obj3D.helper = null;
	
	obj.userData.obj3D.ur = {};
	obj.userData.obj3D.ur.pos = new THREE.Vector3();
	obj.userData.obj3D.ur.q = new THREE.Quaternion();	
			
	if(1==1)
	{
		var id = 0;
		
		obj.traverse( function ( child ) 
		{
			if ( child.isMesh ) 
			{ 
				var boundingBox = child.geometry.boundingBox;
				console.log(child.name, boundingBox.max.x - boundingBox.min.x, boundingBox.max.y - boundingBox.min.y, boundingBox.max.z - boundingBox.min.z);
				
				if(new RegExp( '_est_' ,'i').test( child.name ))
				{
					//console.log(8888888, child.name, child.rotation.x, child.rotation.y, child.rotation.z);
					
					child.visible = false;						
					
					var material = new THREE.MeshPhongMaterial({ color: 0x00ff00, transparent: true, opacity: 1, depthTest: false, lightMap: lightMap_1 });
					
					var geometry = infProject.geometry.centerPoint;
					
					var cube = new THREE.Mesh( geometry, material );
					cube.position.copy(child.position);
					cube.quaternion.copy(child.quaternion);
					cube.visible = false;
					cube.renderOrder = 1;
					//cube.rotation.y += 1;
					//var axesHelper = new THREE.AxesHelper( 0.2 );
					//child.add( axesHelper );							
					
					cube.userData.tag = 'joinPoint';
					cube.userData.id = id;  id++;
					cube.userData.centerPoint = { join: null };						 
					cube.userData.centerPoint.nameRus = child.name;
					
					obj.add( cube );				
				}
			}
		});		
	}
	
	//obj.material.visible = false;		
	
	infProject.scene.array.obj[infProject.scene.array.obj.length] = obj;

	scene.add( obj );	
	console.log(obj);
	//clickO.move = obj;
	
	
	
	renderCamera();	
}









