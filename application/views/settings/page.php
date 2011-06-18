<div class="form">
	<?php if(isset($success)) echo 'Updated';    

	$attributes = array('class' => 'page', 'id' => '');
	echo form_open("settings/page/{$page['page_id']}", $attributes); ?>
	<div id="facebook-information"><h2>Facebook information</h2></div>
	<div id="page-information"><h2>Page information</h2>
	<p>
			<label for="page_name">Page name <span class="required">*</span></label>
			<?php echo form_error('page_name'); ?>
			<br /><input id="page_name" type="text" name="page_name" maxlength="255" value="<?php echo set_value('page_name',$page['page_name']); ?>"  />
	</p>

	<p>
			<label for="page_detail">Page detail</label>
			<?php echo form_error('page_detail'); ?>
			<br /><input id="page_detail" type="text" name="page_detail"  value="<?php echo set_value('page_detail',$page['page_detail']); ?>"  />
	</p>


	<p>
			<?php echo form_submit( 'submit', 'Submit'); ?>
	</p>
	</div>
	<div id="page-admin"><h2>Page admins</h2></div>
	<div id="page-applications"><h2>Page applicatons</h2></div>
	<div id="delete-page"><h2>Delete page</h2></div>
	<?php echo form_close(); ?>


</div>