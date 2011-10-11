          
			<?php // Change the css classes to suit your needs    
			$attributes = array('class' => '', 'id' => 'signup-form');
			echo form_open_multipart('home/signup_form', $attributes); ?>
            <h2>User Information</h2>
            <ul class="form">
              <li>
				<input id="first_name" type="text" name="first_name" maxlength="255" value="<?php echo set_value('first_name'); ?>" <?php echo form_error('first_name') ? 'class="form-error"':''; ?> placeholder="Firstname" /><span>*</span>
			  </li>
              <li>
				<input id="last_name" type="text" name="last_name" maxlength="255" value="<?php echo set_value('last_name'); ?>" <?php echo form_error('last_name') ? 'class="form-error"':''; ?> placeholder="Lastname" /><span>*</span>
			  </li>
              <li>
				<input id="email" type="text" name="email" maxlength="255" value="<?php echo set_value('email'); ?>" <?php echo form_error('email') ? 'class="form-error"':''; ?> placeholder="Your E-Mail Address" /><span>*</span>
			  </li>
            </ul>
            <h2>Company information</h2>
            <ul class="form">
              <li>
				<input id="company_name" type="text" name="company_name" maxlength="255" value="<?php echo set_value('company_name'); ?>" <?php echo form_error('company_name') ? 'class="form-error"':''; ?> placeholder="Company name" /><span>*</span>
			  </li>
              <li>
				<textarea id="company_detail" name="company_detail" <?php echo form_error('company_detail') ? 'class="form-error"':''; ?> placeholder="Write something about your Company"><?php echo set_value('company_detail'); ?></textarea>
			  </li>
              <li class="company-pic">
				<img class="company-image" src="<?php echo $this->socialhappen->get_default_url('company_image')?>" alt="" />
				<?php echo form_error('company_image'); ?>
				<p><b>Your company logo</b></p>
				<div class="upload-pic">
					<span><a>(Change)</a></span>
					<input id="company_image" type="file" name="company_image" />
				</div>
			  </li>
            </ul>
			<div class="agreement">
				<p>By <b>signing up</b> you agree to the <a href="#">terms of use</a> and <a href="">privacy policy</a>.</p>
			</div>
            <div class="buttons">
				<input type="hidden" name="package_id" value="<?php echo $this->input->get('package_id'); ?>"/>
				<input type="hidden" name="payment" value="<?php echo $this->input->get('payment'); ?>"/>
				<p><a class="bt-create-account">Create account</a></p>
			</div>
            <?php 
			echo form_submit('signup-form', 'Submit', 'style="display:none"');
			echo form_close(); ?>