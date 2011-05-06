<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Edit Company</title>
	<?php echo link_tag('css/style.css'); ?>
</head>
<body>
<h1>Edit Company</h1>
<?php
/*
	var $company_id;
	var $company_name = '';
	var $company_address = '';
	var $company_email = '';
	var $company_telephone = ''; 
	var $company_register_date; //not yet implemented
	var $company_username = ''; //not yet implemented
	var $company_password = ''; //not yet implemented
	var $company_facebook_id = ''; //not yet implemented
 */
echo form_open('backend/edit_company/'.$company_id);
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
						  'value'       => set_value('company_name', $company_name),
						  'maxlength'   => '100',
						  'size'        => '50',
						  'class'		=> (form_error('company_name')) ? 'error' : '',
				))?>
				<?php echo form_error('company_name'); ?>
			</li>
			
			<li>
				<?=form_label('Company address*', 'company_address', array(
				'class' => 'company_address',
				'style' => 'color: #000;',
				));?>
				<?=form_input(array(
						  'name'        => 'company_address',
						  'id'          => 'company_address',
						  'value'       => set_value('company_address', $company_address),
						  'maxlength'   => '100',
						  'size'        => '50',
						  'class'		=> (form_error('company_address')) ? 'error' : '',
				))?>
				<?php echo form_error('company_address'); ?>
			</li>
			
			<li>
				<?=form_label('Company Email*', 'company_email', array(
				'class' => 'company_email',
				'style' => 'color: #000;',
				));?>
				<?=form_input(array(
						  'name'        => 'company_email',
						  'id'          => 'company_email',
						  'value'       => set_value('company_email', $company_email),
						  'maxlength'   => '100',
						  'size'        => '50',
						  'class'		=> (form_error('company_email')) ? 'error' : '',
				))?>
				<?php echo form_error('company_email'); ?>
			</li>
			
						<li>
				<?=form_label('Company Telephone*', 'company_telephone', array(
				'class' => 'company_telephone',
				'style' => 'color: #000;',
				));?>
				<?=form_input(array(
						  'name'        => 'company_telephone',
						  'id'          => 'company_telephone',
						  'value'       => set_value('company_telephone', $company_telephone),
						  'maxlength'   => '100',
						  'size'        => '50',
						  'class'		=> (form_error('company_telephone')) ? 'error' : '',
				))?>
				<?php echo form_error('company_telephone'); ?>
			</li>
			
		</ul>
		
		<div class="button-style">
		
		<p>
		<?php echo form_submit(array(
							'name' => 'submit', 
							'value' => 'Save',
							'class' => 'bt-create-now'
							)); ?> or <?php echo anchor('backend', 'Cancel');?>
		</p>
		</div>
		<?=form_close()?>
</body>
</html>