<div class="modal" id="create-company">

	<div class="modal-header">
		<a class="close" data-dismiss="modal">×</a>
		<h3>Create Company</h3>
	</div><?php

	if(!isset($created)) : ?>

		<div class="modal-body"><?php 

			$attributes = array('class' => 'form-horizontal', 'id' => 'create-company-form');
			echo form_open('bar/create_company_bootstrap', $attributes); 

			if(isset($error)) { ?>
				<div class="alert alert-error"><?php echo $error; ?></div><?php
			} ?>
			
			<div class="control-group <?php echo form_error('company_name') ? 'error' : ''; ?>">
				<label for="company_name" class="control-label">Company name</label>
				<div class="controls">
					<input id="company_name" class="span3" type="text" name="company_name" maxlength="255" value="<?php echo set_value('company_name'); ?>" placeholder="Company name" />
					<?php echo form_error('company_name'); ?>
				</div>
			</div>

			<div class="control-group <?php echo form_error('company_detail') ? 'error' : ''; ?>">
				<label for="company_detail" class="control-label">Company detail</label>
				<div class="controls">
					<textarea id="company_detail" class="span3" name="company_detail" placeholder="Company detail"><?php echo set_value('company_detail'); ?></textarea>
					<?php echo form_error('company_detail'); ?>
				</div>
			</div>

			<div class="control-group <?php echo form_error('company_image') ? 'error' : ''; ?>">
				<label for="company_image" class="control-label">Company image</label>
				<div class="controls">
					
					<div class="upload-pic">
						<img src="<?php echo base_url(); ?>assets/images/default/company.png" width="80" height="80" />
						<input id="company_image" type="file" name="company_image" />
					</div>
					<?php echo form_error('company_image'); ?>
				</div>
			</div>
						
			<?php echo form_close(); ?>
		</div>

		<div class="modal-footer">
			<button type="button" class="btn" data-dismiss="modal">Close</button>
			<button type="submit" class="btn btn-primary create-company-submit">Continue</button>
		</div>

	<?php else : ?>
		<div class="modal-body">
			<div class="alert alert-success" data-redirect="<?php echo $redirect; ?>">Company Created</div>
			
		</div>
	<?php endif; ?>

</div>