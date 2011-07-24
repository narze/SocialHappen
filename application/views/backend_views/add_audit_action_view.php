<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Backend | Add New Audit Action</title>
	<?php echo link_tag('css/style.css'); ?>
</head>
<body>
<h1>Add New Audit Action : <?php echo $app_name; ?></h1>
<?php
echo form_open('backend/add_audit_action/'.$app_id);
?>
		<ul>
			<li>
				<?=form_label('Action ID*', 'action_id', array(
				'class' => 'action_id',
				'style' => 'color: #000;',
				));?>
				<?=form_input(array(
						  'name'        => 'action_id',
						  'id'          => 'action_id',
						  'value'       => set_value('action_id'),
						  'maxlength'   => '100',
						  'size'        => '50',
						  'class'		=> (form_error('action_id')) ? 'error' : '',
				))?> *Action ID between 0-999 are reserved for default Audit Action
				<?php echo form_error('action_id'); ?><?php echo $duplicate_action_id;?><?php echo $invalid_action_id;?>
			</li>
			<li>
				<?=form_label('Action Description*', 'description', array(
				'class' => 'description',
				'style' => 'color: #000;',
				));?>
				<?=form_input(array(
						  'name'        => 'description',
						  'id'          => 'description',
						  'value'       => set_value('description'),
						  'maxlength'   => '400',
						  'size'        => '200',
						  'class'		=> (form_error('description')) ? 'error' : '',
				))?>
				<?php echo form_error('description'); ?>
			</li>
			<li>
				<?=form_label('Format String*', 'format_string', array(
				'class' => 'format_string',
				'style' => 'color: #000;',
				));?>
				<?=form_input(array(
						  'name'        => 'format_string',
						  'id'          => 'format_string',
						  'value'       => set_value('format_string'),
						  'maxlength'   => '400',
						  'size'        => '200',
						  'class'		=> (form_error('format_string')) ? 'error' : '',
				))?>
				<?php echo form_error('format_string'); ?>
			</li>
			<li>
				<?=form_checkbox(array(
						  'name'        => 'stat_app',
						  'id'          => 'stat_app',
						  'value'       => 'stat_app',
						  'checked'     => FALSE,
						  'class'		=> (form_error('stat_app')) ? 'error' : '',
				))?>
				<?=form_label('Collect Stat for App', 'stat_app', array(
				'class' => 'stat_app',
				'style' => 'color: #000;',
				));?>
				<?php echo form_error('stat_app'); ?>
			</li>
			
			<li>
				<?=form_checkbox(array(
						  'name'        => 'stat_page',
						  'id'          => 'stat_page',
						  'value'       => 'stat_page',
						  'checked'     => FALSE,
						  'class'		=> (form_error('stat_page')) ? 'error' : '',
				))?>
				<?=form_label('Collect Stat for Page', 'stat_page', array(
				'class' => 'stat_page',
				'style' => 'color: #000;',
				));?>
				<?php echo form_error('stat_page'); ?>
			</li>
			
			<li>
				<?=form_checkbox(array(
						  'name'        => 'stat_campaign',
						  'id'          => 'stat_campaign',
						  'value'       => 'stat_campaign',
						  'checked'     => FALSE,
						  'class'		=> (form_error('stat_campaign')) ? 'error' : '',
				))?>
				<?=form_label('Collect Stat for Campaign', 'stat_campaign', array(
				'class' => 'stat_campaign',
				'style' => 'color: #000;',
				));?>
				<?php echo form_error('stat_campaign'); ?>
			</li>
			
		</ul>
		
		<div class="button-style">
		
		<p>
		<?php echo form_submit(array(
							'name' => 'submit', 
							'value' => 'Add',
							'class' => 'bt-create-now'
							)); ?> or <?php echo anchor('backend/list_audit_action/'.$app_id, 'Cancel');?>	
		</p>
		</div>
		<?=form_close()?>
</body>
</html>