<?php // Change the css classes to suit your needs    
		$attributes = array('class' => '', 'id' => '');
		echo form_open('signup/form', $attributes); ?>
		<div id="user-information"><h2>User information</h2>
			<p>
			        <label for="first_name">First name <span class="required">*</span></label>
			        <?php echo form_error('first_name'); ?>
			        <br /><input id="first_name" type="text" name="first_name" maxlength="255" value="<?php echo set_value('first_name'); ?>"  />
			</p>
			
			<p>
			        <label for="last_name">Last name <span class="required">*</span></label>
			        <?php echo form_error('last_name'); ?>
			        <br /><input id="last_name" type="text" name="last_name" maxlength="255" value="<?php echo set_value('last_name'); ?>"  />
			</p>
			
			<p>
			        <label for="email">Email <span class="required">*</span></label>
			        <?php echo form_error('email'); ?>
			        <br /><input id="email" type="text" name="email" maxlength="255" value="<?php echo set_value('email'); ?>"  />
			</p>
			
			<p>
			        <label for="user_image">User image</label>
			        <?php echo form_error('user_image'); ?>
			        <br /><input id="user_image" type="text" name="user_image" maxlength="255" value="<?php echo set_value('user_image'); ?>"  />
			</p>

		</div>
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
			        <br /><input id="company_image" type="text" name="company_image" maxlength="255" value="<?php echo set_value('company_image'); ?>"  />
			</p>
		</div>
		
		<p>
		        <?php echo form_submit( 'submit', 'Submit'); ?>
		</p>
		
		<?php echo form_close(); ?>