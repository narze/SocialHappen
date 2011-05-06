<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Add new app</title>
	<?php echo link_tag('css/style.css'); ?>
</head>
<body>
<h1>Add new app</h1>
<?php
/*
	var $app_id = '';
	var $app_name = '';
	var $app_maintainance = '';
	var $app_show_in_list = '';
	var $app_path = '';
	var $app_description = '';
 */
echo form_open('backend/add_new_app');
?>
		<ul>
			<li>
				<?=form_label('App Name*', 'app_name', array(
				'class' => 'app_name',
				'style' => 'color: #000;',
				));?>
				<?=form_input(array(
						  'name'        => 'app_name',
						  'id'          => 'app_name',
						  'value'       => set_value('app_name'),
						  'maxlength'   => '100',
						  'size'        => '50',
						  'class'		=> (form_error('app_name')) ? 'error' : '',
				))?>
				<?php echo form_error('app_name'); ?>
			</li>
			
			<li>
				<?=form_label('App Description*', 'app_description', array(
				'class' => 'app_description',
				'style' => 'color: #000;',
				));?>
				<?=form_input(array(
						  'name'        => 'app_description',
						  'id'          => 'app_description',
						  'value'       => set_value('app_description'),
						  'maxlength'   => '400',
						  'size'        => '200',
						  'class'		=> (form_error('app_description')) ? 'error' : '',
				))?>
				<?php echo form_error('app_description'); ?>
			</li>
			
			<li>
				<?=form_label('App URL*', 'app_url', array(
				'class' => 'app_url',
				'style' => 'color: #000;',
				));?>
				<?=form_input(array(
						  'name'        => 'app_url',
						  'id'          => 'app_url',
						  'value'       => set_value('app_url'),
						  'maxlength'   => '300',
						  'size'        => '200',
						  'class'		=> (form_error('app_url')) ? 'error' : '',
				))?>
				<?php echo form_error('app_url'); ?>
			</li>
			
			<li>
				<?=form_label('App Install URL*', 'app_install_url', array(
				'class' => 'app_install_url',
				'style' => 'color: #000;',
				));?>
				<?=form_input(array(
						  'name'        => 'app_install_url',
						  'id'          => 'app_install_url',
						  'value'       => set_value('app_install_url'),
						  'maxlength'   => '300',
						  'size'        => '200',
						  'class'		=> (form_error('app_install_url')) ? 'error' : '',
				))?>
				<?php echo form_error('app_install_url'); ?>
			</li>
			
			<li>
				<?=form_label('App Config URL*', 'app_config_url', array(
				'class' => 'app_config_url',
				'style' => 'color: #000;',
				));?>
				<?=form_input(array(
						  'name'        => 'app_config_url',
						  'id'          => 'app_config_url',
						  'value'       => set_value('app_config_url'),
						  'maxlength'   => '300',
						  'size'        => '200',
						  'class'		=> (form_error('app_config_url')) ? 'error' : '',
				))?>
				<?php echo form_error('app_config_url'); ?>
			</li>
			
			<li>
				<?=form_checkbox(array(
						  'name'        => 'app_support_page_tab',
						  'id'          => 'app_support_page_tab',
						  'value'       => 'app_support_page_tab',
						  'checked'     => FALSE,
						  'class'		=> (form_error('app_support_page_tab')) ? 'error' : '',
				))?>
				<?=form_label('App Support Page Tab', 'app_support_page_tab', array(
				'class' => 'app_support_page_tab',
				'style' => 'color: #000;',
				));?>
				<?php echo form_error('app_support_page_tab'); ?>
			</li>
			
		</ul>
		
		<div class="button-style">
		
		<p>
		<?php echo form_submit(array(
							'name' => 'submit', 
							'value' => 'Add',
							'class' => 'bt-create-now'
							)); ?> or <?php echo anchor('backend', 'Cancel');?>	
		</p>
		</div>
		<?=form_close()?>
</body>
</html>