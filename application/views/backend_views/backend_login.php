<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>SocialHappen Backend</title>
	<?php echo link_tag('css/style.css'); ?>
</head>
<body>
<h1>Login</h1>
<?php
echo form_open('backend/');
?>
		<ul>
			<li>
				<?=form_label('Key 1', 'key1', array(
				'class' => 'key1',
				'style' => 'color: #000;',
				));?>
				<?=form_password(array(
						  'name'        => 'key1',
						  'id'          => 'key1',
						  'value'       => set_value('key1'),
						  'maxlength'   => '30',
						  'size'        => '50',
						  'class'		=> (form_error('key1')) ? 'error' : '',
				))?>
				<?php echo form_error('key1'); ?>
			</li>
			
			<li>
				<?=form_label('Key 2', 'key2', array(
				'class' => 'key2',
				'style' => 'color: #000;',
				));?>
				<?=form_password(array(
						  'name'        => 'key2',
						  'id'          => 'key2',
						  'value'       => set_value('key2'),
						  'maxlength'   => '30',
						  'size'        => '50',
						  'class'		=> (form_error('key2')) ? 'error' : '',
				))?>
				<?php echo form_error('key2'); ?>
				<?=form_hidden(array(
									'time' => time()
									)
					);?>
			</li>
		</ul>
		
		<div class="button-style">
		<p>
		<?php echo form_submit(array(
							'name' => 'submit', 
							'value' => 'Login'
							)); ?>
		</p>
		</div>
		<?=form_close()?>
</body>
</html>