<script>
$(document).ready(function(){

<? // delet коммент ?>
$(document).on('click', '[load_3d]', function () {
$.ajax({
type: "POST",					
url: '/page/3d/otop_3d.php',
beforeSend: function(){ $("#loader").css({"display":"block"}); },
success: function(data){ $("#loader").css({"display":"none"}); 
$('[load_3d]').html(data);
}
});
});
<? // delet коммент ?>


});
</script>




<div class="infotext">

<h1><?=$cont['h1']?></h1>



<!-- otop_schema_1 -->
<div class="ads_3d_1">
<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-5657772487610973" data-ad-slot="9260811040" data-ad-format="auto"></ins>
<script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
</div>
<!-- otop_schema_1 -->




<? // -------000 ?>
<div id="frame_3d" class="frame_3d">


<canvas class="emscripten" id="canvas" oncontextmenu="event.preventDefault()" height="100%" width="100%"></canvas>
<script src="/page/3d/progress.js"></script>
<script type='text/javascript'>
  // connect to canvas
  var Module = {
    TOTAL_MEMORY: 268435456,
    filePackagePrefixURL: "/page/3d/<?=$cont['content']?>/",
    memoryInitializerPrefixURL: "/page/3d/<?=$cont['content']?>/",
    preRun: [],
    postRun: [],
    print: (function() {
      return function(text) {
        console.log (text);
      };
    })(),
    printErr: function(text) {
      console.error (text);
    },
    canvas: document.getElementById('canvas'),
    progress: null,
    setStatus: function(text) {
      if (this.progress == null) 
      {
        if (typeof UnityProgress != 'function')
          return;
        this.progress = new UnityProgress (canvas);
      }
      if (!Module.setStatus.last) Module.setStatus.last = { time: Date.now(), text: '' };
      if (text === Module.setStatus.text) return;
      this.progress.SetMessage (text);
      var m = text.match(/([^(]+)\((\d+(\.\d+)?)\/(\d+)\)/);
      if (m)
        this.progress.SetProgress (parseInt(m[2])/parseInt(m[4]));
      if (text === "") 
        this.progress.Clear()
    },
    totalDependencies: 0,
    monitorRunDependencies: function(left) {
      this.totalDependencies = Math.max(this.totalDependencies, left);
      Module.setStatus(left ? 'Preparing... (' + (this.totalDependencies-left) + '/' + this.totalDependencies + ')' : 'All downloads complete.');
    }
  };
  Module.setStatus('Downloading (0.0/1)');
  

  
  	var myHeight = 100; var myWidth = 100;
	
	OnLoaded(); 
	  
	function OnLoaded(){
	  reportSize();
	  document.getElementById('canvas').style.width = myWidth + 'px';
	  document.getElementById('canvas').style.height = myHeight + 'px';	
	  
	  <? // измение размера окна ?>
	  window.onresize = function(){
		  reportSize();
		  document.getElementById('canvas').style.width = myWidth + 'px';
		  document.getElementById('canvas').style.height = myHeight + 'px';		  
	  }
	}
	
	function reportSize() {
	  myWidth = 0; myHeight = 0;
	  if( typeof( window.innerWidth ) == 'number' ) {
	    //Non-IE
	    myWidth = document.getElementById('frame_3d').clientWidth;
	    myHeight = document.getElementById('frame_3d').clientHeight;
	  } else {
	    myWidth = 995; myHeight = 540;
	  }
	}
	
	
</script>
<script src="/page/3d/config.js"></script>
<script src="/page/3d/<?=$cont['content']?>/fileloader.js"></script>
<script>if (!(!Math.fround)) {
  var script = document.createElement('script');
  script.src = "/page/3d/<?=$cont['content']?>/Win.js";
  document.body.appendChild(script);
} else {
  var codeXHR = new XMLHttpRequest();
  codeXHR.open('GET', '/page/3d/<?=$cont['content']?>/Win.js', true);
  codeXHR.onload = function() {
    var code = codeXHR.responseText;
    if (!Math.fround) { 
try {
  console.log('optimizing out Math.fround calls');
  var m = /var ([^=]+)=global\.Math\.fround;/.exec(code);
  var minified = m[1];
  if (!minified) throw 'fail';
  var startAsm = code.indexOf('// EMSCRIPTEN_START_FUNCS');
  var endAsm = code.indexOf('// EMSCRIPTEN_END_FUNCS');
  var asm = code.substring(startAsm, endAsm);
  do {
    var moar = false; // we need to re-do, as x(x( will not be fixed
    asm = asm.replace(new RegExp('[^a-zA-Z0-9\\$\\_]' + minified + '\\(', 'g'), function(s) { moar = true; return s[0] + '(' });
  } while (moar);
  code = code.substring(0, startAsm) + asm + code.substring(endAsm);
  code = code.replace("'use asm'", "'almost asm'");
} catch(e) { console.log('failed to optimize out Math.fround calls ' + e) }
 }

    var blob = new Blob([code], { type: 'text/javascript' });
    codeXHR = null;
    var src = URL.createObjectURL(blob);
    var script = document.createElement('script');
    script.src = URL.createObjectURL(blob);
    script.onload = function() {
      URL.revokeObjectURL(script.src);
    };
    document.body.appendChild(script);
  };
  codeXHR.send(null);
}
</script>

</div>
<? // -------000 ?>







<div class="line_vd"></div>


<!-- otop_videos_362x300 -->
<div class="ads_370x320">
<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<ins class="adsbygoogle" style="display:inline-block;width:362px;height:300px" data-ad-client="ca-pub-5657772487610973" data-ad-slot="7606725045"></ins>
<script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
</div>
<!-- otop_videos_362x300 -->



<? // ссылки ?>
<div class="b_link">
<?
$sql = "SELECT p.url AS url, v.title AS title
FROM content_videos v
INNER JOIN page AS p on p.id_url = v.id_url 
ORDER BY RAND() LIMIT 6";
$r = $db->query($sql);
$link = $r->fetchAll(PDO::FETCH_ASSOC);

foreach ($link as $p) {?>
<a href="<?=$p['url']?>" class="b_link_a">
<img src="/img/link<?=$p['url']?>.jpg">
<div class="b_link_t"><?=$p['title']?></div>
</a>
<?}?>
</div>
<? // ссылки ?>

<div>
<div class="clear"></div>
</div>

<div class="otstup6"></div>



</div>
