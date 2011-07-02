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
				<?=form_label('App Type*', 'app_type_id', array(
				'class' => 'app_type_id',
				'style' => 'color: #000;',
				));?>
				<?
					$options = array(
					  '1'  => '1',
					  '2'    => '2',
					  '3'   => '3'
					);
				?>
				<?=form_dropdown('app_type_id', $options);?>
				<?php echo form_error('app_type_id'); ?>
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
				<?=form_label('App Install URL*, parameters: {company_id}, {user_id}, {[page_id]}, {[force]}', 'app_install_url', array(
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
				<?=form_label('App Install to Page URL*, parameters: {app_install_id}, {user_id}, {page_id}, {force}', 'app_install_page_url', array(
				'class' => 'app_install_page_url',
				'style' => 'color: #000;',
				));?>
				<?=form_input(array(
						  'name'        => 'app_install_page_url',
						  'id'          => 'app_install_page_url',
						  'value'       => set_value('app_install_page_url'),
						  'maxlength'   => '300',
						  'size'        => '200',
						  'class'		=> (form_error('app_install_page_url')) ? 'error' : '',
				))?>
				<?php echo form_error('app_install_page_url'); ?>
			</li>
			
			<li>
				<?=form_label('App Config URL*, parameters: {app_install_id}, {user_id}, {app_install_secret_key}', 'app_config_url', array(
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
				<?=form_label('Facebook app API key*', 'app_facebook_api_key', array(
				'class' => 'app_facebook_api_key',
				'style' => 'color: #000;',
				));?>
				<?=form_input(array(
						  'name'        => 'app_facebook_api_key',
						  'id'          => 'app_facebook_api_key',
						  'value'       => set_value('app_facebook_api_key'),
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