<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>

<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta name="description" content="SocialHappen" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>SocialHappen<?php  echo " - $title";?></title>
<?php 
	foreach($styles as $one) {
		if($one){
			if(strrchr($one, '.') === '.css') {
				echo '<link rel="stylesheet" type="text/css"  href="'.$one.'" />'."\n";
			} else {
				echo '<link rel="stylesheet" type="text/css"  href="'.base_url().'assets/css/'.$one.'.css" />'."\n";
			}
		}
	}
?>
</head>
<?php flush(); ?>
<body>
<?php 
	foreach($body_views as $view_name => $data) {
		$this->load->view($view_name, $data);
	}

	foreach($scripts as $one) {
		if ($one) {
			if(strrchr($one, '.') === '.js') {
				echo '<script type="text/javascript" src="'.$one.'"></script>'."\n";
			} else {
				echo '<script type="text/javascript" src="'.base_url().'assets/js/'.$one.'.js"></script>'."\n";
			}
		}
	}
?>
</body>
</html>
