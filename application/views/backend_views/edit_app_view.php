<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Edit App Information</title>
	<?php echo link_tag('css/style.css'); ?>
</head>
<body>
<h1>Edit App Information</h1>
<?php
echo form_open_multipart('backend/edit_app/'.$app_id);
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
						  'value'       => set_value('app_name', $app_name),
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
					  $this->socialhappen->get_k('app_type','Page Only')  => "Page Only",
					  $this->socialhappen->get_k('app_type','Support Page')  => "Support Page",
					  $this->socialhappen->get_k('app_type','Standalone')  => "Standalone"
					);
				?>
				<?=form_dropdown('app_type_id', $options, $app_type_id);?>
				<?php echo form_error('app_type_id'); ?>
			</li>

			<li>
				<?=form_label('App Icon', 'app_icon', array(
				'class' => 'app_icon',
				'style' => 'color: #000;',
				));
				if($app_icon) { ?>
				<img src="<?php echo $app_icon; ?>" width="16" height="16" />
				<?php } ?>
				<input id="app_icon" type="file" name="app_icon" />
				<input type="hidden" name="app_icon_old" value="<?php echo $app_icon; ?>">
				<?php echo form_error('app_icon'); ?>
			</li>

			<li>
				<?=form_label('App Image', 'app_image', array(
				'class' => 'app_image',
				'style' => 'color: #000;',
				));
				if($app_image) { ?>
				<img src="<?php echo imgsize($app_image, 'small'); ?>" />
				<?php } ?>
				<input id="app_image" type="file" name="app_image" />
				<input type="hidden" name="app_image_old" value="<?php echo $app_image; ?>">
				<?php echo form_error('app_image'); ?>
			</li>

			<li>
				<?=form_label('App Banner', 'app_banner', array(
				'class' => 'app_banner',
				'style' => 'color: #000;',
				));
				if($app_banner) { ?>
				<img src="<?php echo $app_banner; ?>" />
				<?php } ?>
				<input id="app_banner" type="file" name="app_banner" />
				<input type="hidden" name="app_banner_old" value="<?php echo $app_banner; ?>">
				<?php echo form_error('app_banner'); ?>
			</li>

			<li>
				<?=form_label('App Description*', 'app_description', array(
				'class' => 'app_description',
				'style' => 'color: #000;',
				));?>
				<?=form_input(array(
						  'name'        => 'app_description',
						  'id'          => 'app_description',
						  'value'       => set_value('app_description', $app_description),
						  'maxlength'   => '400',
						  'size'        => '200',
						  'class'		=> (form_error('app_description')) ? 'error' : '',
				))?>
				<?php echo form_error('app_description'); ?>
			</li>



			<li>
				<?=form_label('App URL*, parameter: {app_install_id}', 'app_url', array(
				'class' => 'app_url',
				'style' => 'color: #000;',
				));?>
				<?=form_input(array(
						  'name'        => 'app_url',
						  'id'          => 'app_url',
						  'value'       => set_value('app_url', $app_url),
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
						  'value'       => set_value('app_install_url', $app_install_url),
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
						  'value'       => set_value('app_install_page_url', $app_install_page_url),
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
						  'value'       => set_value('app_config_url', $app_config_url),
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
						  'value'       => set_value('app_facebook_api_key', $app_facebook_api_key),
						  'maxlength'   => '300',
						  'size'        => '200',
						  'class'		=> (form_error('app_facebook_api_key')) ? 'error' : '',
				))?>
				<?php echo form_error('app_facebook_api_key'); ?>
			</li>

			<li>
				<?=form_label('App Config Facebook Canvas Path', 'app_config_facebook_canvas_path', array(
				'class' => 'app_config_facebook_canvas_path',
				'style' => 'color: #000;',
				));?>
				<?=form_input(array(
						  'name'        => 'app_config_facebook_canvas_path',
						  'id'          => 'app_config_facebook_canvas_path',
						  'value'       => set_value('app_config_facebook_canvas_path', $app_config_facebook_canvas_path),
						  'maxlength'   => '300',
						  'size'        => '200',
						  'class'		=> (form_error('app_config_facebook_canvas_path')) ? 'error' : '',
				))?>
				<?php echo form_error('app_config_facebook_canvas_path'); ?>
			</li>


			<li>
				<?=form_checkbox(array(
						  'name'        => 'app_support_page_tab',
						  'id'          => 'app_support_page_tab',
						  'value'       => 'app_support_page_tab',
						  'checked'     => $app_support_page_tab,
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
							'value' => 'Save',
							'class' => 'bt-create-now'
							)); ?> or <?php echo anchor('backend/app', 'Cancel');?>
		</p>
		</div>
		<?=form_close()?>
</body>
</html>