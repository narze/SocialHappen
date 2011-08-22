<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Edit Package</title>
	<?php echo link_tag('css/style.css'); ?>
</head>
<body>
<h1>Edit Package</h1>
<?php
echo form_open_multipart('backend/edit_package/'.$package_id);
?>
		<ul>
			<li>
				<?=form_label('Package Image', 'package_image', array(
				'class' => 'package_image',
				'style' => 'color: #000;',
				));
				if($package_image) { ?>
				<img src="<?php echo str_replace('_o','_t',$package_image); ?>" />
				<?php } ?>
				<input id="package_image" type="file" name="package_image" />
				<input type="hidden" name="package_image_old" value="<?php echo $package_image; ?>">
				<?php echo form_error('package_image'); ?>
			</li>
			
			<li>
				<?=form_label('Package Name*', 'package_name', array(
				'class' => 'package_name',
				'style' => 'color: #000;',
				));?>
				<?=form_input(array(
						  'name'        => 'package_name',
						  'id'          => 'package_name',
						  'value'       => set_value('package_name', $package_name),
						  'maxlength'   => '100',
						  'size'        => '50',
						  'class'		=> (form_error('package_name')) ? 'error' : '',
				))?>
				<?php echo form_error('package_name'); ?>
			</li>
			
			<li>
				<?=form_label('Package Detail', 'package_detail', array(
				'class' => 'package_detail',
				'style' => 'color: #000;',
				));?>
				<br />
				<?=form_textarea(array(
						  'name'        => 'package_detail',
						  'id'          => 'package_detail',
						  'value'       => set_value('package_detail', $package_detail),
						  'rows'		=> '4',
						  'cols'        => '30',
						  'class'		=> (form_error('package_detail')) ? 'error' : '',
				))?>
				<?php echo form_error('package_detail'); ?>
			</li>
			
			<li>
				<?=form_label('Max companies*', 'package_max_companies	', array(
				'class' => 'package_max_companies	',
				'style' => 'color: #000;',
				));?>
				<?=form_input(array(
						  'name'        => 'package_max_companies',
						  'id'          => 'package_max_companies',
						  'value'       => set_value('package_max_companies', $package_max_companies),
						  'maxlength'   => '100',
						  'size'        => '50',
						  'class'		=> (form_error('package_max_companies')) ? 'error' : '',
				))?>
				<?php echo form_error('package_max_companies'); ?>
			</li>			

			<li>
				<?=form_label('Max pages*', 'package_max_pages	', array(
				'class' => 'package_max_pages	',
				'style' => 'color: #000;',
				));?>
				<?=form_input(array(
						  'name'        => 'package_max_pages',
						  'id'          => 'package_max_pages',
						  'value'       => set_value('package_max_pages', $package_max_pages),
						  'maxlength'   => '100',
						  'size'        => '50',
						  'class'		=> (form_error('package_max_pages')) ? 'error' : '',
				))?>
				<?php echo form_error('package_max_pages'); ?>
			</li>
			
			<li>
				<?=form_label('Max users*', 'package_max_users	', array(
				'class' => 'package_max_users	',
				'style' => 'color: #000;',
				));?>
				<?=form_input(array(
						  'name'        => 'package_max_users',
						  'id'          => 'package_max_users',
						  'value'       => set_value('package_max_users', $package_max_users),
						  'maxlength'   => '100',
						  'size'        => '50',
						  'class'		=> (form_error('package_max_users')) ? 'error' : '',
				))?>
				<?php echo form_error('package_max_users'); ?>
			</li>
			
			<li>
				<?=form_label('Price', 'package_price	', array(
				'class' => 'package_price	',
				'style' => 'color: #000;',
				));?>
				<?=form_input(array(
						  'name'        => 'package_price',
						  'id'          => 'package_price',
						  'value'       => set_value('package_price', $package_price),
						  'maxlength'   => '100',
						  'size'        => '50',
						  'class'		=> (form_error('package_price')) ? 'error' : '',
				))?>
				<?php echo form_error('package_price'); ?>
			</li>
			
			<li>
				<input type="checkbox" id="package_custom_badge" name="package_custom_badge" <?php echo set_checkbox('package_custom_badge', NULL, $package_custom_badge == 1); ?>> 
				<?=form_label('Custom badge', 'package_custom_badge', array(
				'class' => 'package_custom_badge',
				'style' => 'color: #000;',
				));?>
				<?php echo form_error('package_custom_badge'); ?>
			</li>
			
			<li>
				<?=form_label('Duration*', 'package_duration	', array(
				'class' => 'package_duration	',
				'style' => 'color: #000;',
				));?>
				<?=form_input(array(
						  'name'        => 'package_duration',
						  'id'          => 'package_duration',
						  'value'       => set_value('package_duration', $package_duration),
						  'maxlength'   => '100',
						  'size'        => '50',
						  'class'		=> (form_error('package_duration')) ? 'error' : '',
				))?>
				<?php echo form_error('package_duration'); ?>
				(EX. 1month, 1year)
			</li>
		</ul>
		
		<div class="button-style">
		
		<p>
		<?php echo form_submit(array(
							'name' => 'submit', 
							'value' => 'Save',
							'class' => 'bt-create-now'
							)); ?> or <?php echo anchor('backend/packages', 'Cancel');?>	
		</p>
		</div>
		<?=form_close()?>
</body>
</html>