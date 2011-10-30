<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
<script>
	var base_url = "<?php echo base_url(); ?>";
	<?php if(isset($vars)) :
		foreach($vars as $name => $value) :
			echo "var {$name} = '{$value}';\n";
		endforeach; 
	endif; ?>
</script>
<script src="<?php echo base_url().'assets/js/xd/xd.js'; ?>" type="text/javascript"></script>
<script>
	onload = function(){
		send({sh_message:'loaded'});
	};
</script>