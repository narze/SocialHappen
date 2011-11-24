<link rel="stylesheet" type="text/css"  href="<?php echo base_url().'assets/css/common/fancybox/jquery.fancybox-1.3.4.css'; ?>" />
<link rel="stylesheet" type="text/css"  href="<?php echo base_url().'assets/css/common/smoothness/jquery-ui-1.8.9.custom.css'; ?>" />

<script>
	
	var base_url = "<?php echo base_url(); ?>";
	<?php if(isset($vars)) :
		foreach($vars as $name => $value) :
			echo "var {$name} = '{$value}';\n";
		endforeach; 
	endif; ?>
</script>
<script src="<?php echo base_url().'assets/js/api/bar.js'; ?>" type="text/javascript"></script>
<span class="XD" style="position: absolute; top: -10000px; height:0; width:0"><iframe id="xd_sh" frameborder="0" height="100px" width="100px" scrolling="no" marginheight="0" marginwidth="0" src="<?php echo base_url().'xd';?>"></iframe></span>
<script>
	iframe_src =  document.getElementById("xd_sh").src + '#' + encodeURIComponent(document.location.href);
	document.getElementById("xd_sh").src = iframe_src;
</script>
<div id="sh-bar" class="header">
   
</div>
<script src="<?php echo base_url().'assets/js/api/socket.io.min.js';?>"></script>