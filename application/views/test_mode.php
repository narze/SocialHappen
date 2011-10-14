<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>SocialHappen Test mode</title>
	<?php echo link_tag('css/style.css'); ?>
</head>
<body>
<h1>You've reached SH beta page!</h1>
<?php
$url = '';
if(isset($_GET['next'])) {
	$url = $_GET['next'];
} else {
	$host = $_SERVER['HTTP_HOST'];
	$self = $_SERVER['PHP_SELF'];
	$query = !empty($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : null;
	$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    
	$url = !empty($query) ? "{$protocol}{$host}{$self}?{$query}" : "{$protocol}{$host}{$self}";
	$url = str_replace("index.php/","",$url);
}
echo form_open($url);
?>
		<ul>
			<li>
				<?php echo form_label('Y U NO PASS', 'test_mode_password', array(
				'style' => 'color: #000;',
				));?>
				<?php echo form_password(array(
						  'name'        => 'test_mode_password',
						  'id'          => 'test_mode_password',
						  'value'       => set_value('test_mode_password'),
						  'maxlength'   => '30',
						  'size'        => '50',
						  'class'		=> (form_error('test_mode_password')) ? 'error' : '',
				))?>
				<?php echo form_error('test_mode_password'); ?>
			</li>
		</ul>
		
		<div class="button-style">
		<p>
		<?php echo form_submit(array(
							'name' => 'submit', 
							'value' => 'GO!'
							)); ?>
		</p>
		</div>
		<?php echo form_close()?>
		<?php die(); ?>
</body>
</html>
