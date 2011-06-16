<div class="form">
	<?php if(isset($success)) echo 'Updated';

		$attributes = array('class' => 'company', 'id' => '');
		echo form_open("settings/company/{$company['company_id']}", $attributes); ?>
		<div id="company-information"><h2>Company information</h2>
		<p>
				<label for="company_name">Company name <span class="required">*</span></label>
				<?php echo form_error('company_name'); ?>
				<br /><input id="company_name" type="text" name="company_name" maxlength="255" value="<?php echo set_value('company_name',$company['company_name']); ?>"  />
		</p>

		<p>
				<label for="company_detail">Company detail</label>
				<?php echo form_error('company_detail'); ?>
				<br /><input id="company_detail" type="text" name="company_detail" maxlength="255" value="<?php echo set_value('company_detail',$company['company_detail']); ?>"  />
		</p>

		<p>
				<label for="company_email">Contact email <span class="required">*</span></label>
				<?php echo form_error('company_email'); ?>
				<br /><input id="company_email" type="text" name="company_email" maxlength="255" value="<?php echo set_value('company_email',$company['company_email']); ?>"  />
		</p>

		<p>
				<label for="company_telephone">Contact telephone <span class="required">*</span></label>
				<?php echo form_error('company_telephone'); ?>
				<br /><input id="company_telephone" type="text" name="company_telephone" maxlength="20" value="<?php echo set_value('company_telephone',$company['company_telephone']); ?>"  />
		</p>

		<p>
				<label for="company_website">Company website <span class="required">*</span></label>
				<?php echo form_error('company_website'); ?>
				<br /><input id="company_website" type="text" name="company_website" maxlength="255" value="<?php echo set_value('company_website',$company['company_website']); ?>"  />
		</p>

		</div>
			
			<div id="company-application"><h2>Company application</h2></div>
			<div id="delete-company"><h2>Delete company</h2></div>
			
		<p>
				<?php echo form_submit( 'submit', 'Submit'); ?>
		</p>

	<?php echo form_close(); ?>

</div>