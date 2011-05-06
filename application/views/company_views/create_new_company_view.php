<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Create new company</title>
	<?php echo link_tag('css/style.css'); ?>
</head>
<body>
<h1>Create new company profile</h1>
<?php
/*
 * 	var $company_id = '';
	var $company_name = '';
	var $company_address = '';
	var $company_email = '';
	var $company_telephone = '';
	var $company_register_date = '';
	var $company_username = '';
	var $company_password = '';
	var $company_facebook_id = '';
 */
echo form_open('company/create_new_company');
?>
		<ul>
			<li>
				<?=form_label('Company Name*', 'company_name', array(
				'class' => 'company_name',
				'style' => 'color: #000;',
				));?>
				<?=form_input(array(
						  'name'        => 'company_name',
						  'id'          => 'company_name',
						  'value'       => set_value('company_name'),
						  'maxlength'   => '100',
						  'size'        => '50',
						  'class'		=> (form_error('company_name')) ? 'error' : '',
				))?>
				<?php echo form_error('company_name'); ?>
			</li>
			
			<li>
				<?=form_label('Company Address', 'company_address', array(
				'class' => 'company_address',
				'style' => 'color: #000;',
				));?>
				<?=form_input(array(
						  'name'        => 'company_address',
						  'id'          => 'company_address',
						  'value'       => set_value('company_address'),
						  'maxlength'   => '100',
						  'size'        => '50',
						  'class'		=> (form_error('company_address')) ? 'error' : '',
				))?>
			</li>
			
			<li>
				<?=form_label('Company Email*', 'company_email', array(
				'class' => 'company_email',
				'style' => 'color: #000;',
				));?>
				<?=form_input(array(
						  'name'        => 'company_email',
						  'id'          => 'company_email',
						  'value'       => set_value('company_email'),
						  'maxlength'   => '100',
						  'size'        => '50',
						  'class'		=> (form_error('company_email')) ? 'error' : '',
				))?>
				<?php echo form_error('company_email'); ?>
			</li>
			
			<li>
				<?=form_label('Company Telephone', 'company_telephone', array(
				'class' => 'company_telephone',
				'style' => 'color: #000;',
				));?>
				<?=form_input(array(
						  'name'        => 'company_telephone',
						  'id'          => 'company_telephone',
						  'value'       => set_value('company_telephone'),
						  'maxlength'   => '100',
						  'size'        => '50',
						  'class'		=> (form_error('company_telephone')) ? 'error' : '',
				))?>
			</li>

		</ul>
		
		<?=form_checkbox(array(
						'name'        => 'accept',
						'id'          => 'accept',
						'value'       => 'accept',
						'checked'     => FALSE,
				))?>
		<?=form_label('accept agreement', 'accept', array(
			'class' => 'country',
			'style' => 'color: #000;',
			));?>
		<?php echo form_error('accept'); ?>
		<div class="button-style">
		
		<p>
		<?php echo form_submit(array(
							'name' => 'submit', 
							'value' => 'Create',
							'class' => 'bt-create-now'
							)); ?> or <?php echo anchor('/admin', 'Cancel');?>	
		</p>
		</div>
		<?=form_close()?>
</body>
</html>