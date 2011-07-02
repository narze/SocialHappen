<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>

<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta name="description" content="SocialHappen" />

<title>SocialHappen<?php if (isset($title)) { echo " - $title"; }?></title>
<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js"></script>

<script type="text/javascript">
	<?php if(isset($vars)) :
	foreach($vars as $name => $value) :
		echo "var {$name} = '{$value}';\n";
	endforeach; 
	var base_url = "<?php echo base_url(); ?>";
	var image_url = base_url + "/assets/images/";
	var user_companies = <?php echo json_encode(issetor($user_companies)); ?>;
endif; ?>
	//var assets_url = "<?php echo base_url().'assets/';?>";
</script>
<?php echo '<script type="text/javascript" src="'.base_url().'assets/js/functions.js"></script>'; 
if(isset($script)) :
	foreach($script as $one) :
		if ($one) echo '<script type="text/javascript" src="'.base_url().'assets/js/'.$one.'.js"></script>'."\n";
	endforeach; 
endif;
if(isset($style)) :
	foreach($style as $one) :
		if($one){
			list($class, $id) = explode('/', $one, 2);
			echo '<link class="'.$class.'" id="'.$id.'" rel="stylesheet" type="text/css"  href="'.base_url().'assets/css/'.$one.'.css" />'."\n";
		}
	endforeach; 
endif; 
?>
</head>
<body>