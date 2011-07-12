<?php
		$attributes = array('class' => 'create-company-form', 'id' => '');
		echo form_open('home/create_company_form', $attributes); ?>
		<div id="company-information"><h2>Company information</h2>
			<p>
			        <label for="company_name">Company name <span class="required">*</span></label>
			        <?php echo form_error('company_name'); ?>
			        <br /><input id="company_name" type="text" name="company_name" maxlength="255" value="<?php echo set_value('company_name'); ?>"  />
			</p>
			
			<p>
			        <label for="company_detail">Company detail</label>
			        <?php echo form_error('company_detail'); ?>
			        <br /><input id="company_detail" type="text" name="company_detail"  value="<?php echo set_value('company_detail'); ?>"  />
			</p>
			
			<p>
					<label for="company_image">Company image</label>
					<?php echo form_error('company_image'); ?>
					<br /><input id="company_image" type="file" name="company_image" />
			</p>
		</div>
		
		<p>
		        <?php echo form_submit( 'submit-form', 'Submit'); ?>
		</p>
		
		<?php echo form_close(); ?>