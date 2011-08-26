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
}
echo form_open($url);
?>
		<ul>
			<li>
				<?=form_label('Y U NO PASS', 'test_mode_password', array(
				'style' => 'color: #000;',
				));?>
				<?=form_password(array(
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
		<?=form_close()?>
		<?php die(); ?>
</body>
</html>