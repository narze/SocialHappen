<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>SH - Invite</title> 	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js" language="javascript"></script>
	<script type="text/javascript">
		var base_url = '<?php echo base_url();?>';
	</script>
</head>
<body>

	<div>Invite Status</div>
	<?php
		foreach($invite as $key => $value){
			if(is_array($value) && $key=='target_facebook_id_list'){
				echo '<div>Target Facebook ID<ul>';
				
				foreach($value as $arr_key => $arr_val){
					echo '<li>'.$arr_val.'</li>';
				}
				echo  '<ul></div>';
				
			}else{
				echo '<div>'.$key.' : '.$value.'</div>';
			}
		}
		
	?>
		
</body>
</html>