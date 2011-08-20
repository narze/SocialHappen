          
			<?php // Change the css classes to suit your needs    
			$attributes = array('class' => '', 'id' => 'signup-form');
			echo form_open_multipart('home/signup_form', $attributes); ?>
            <h2>User Information</h2>
            <ul class="form">
              <li>
				<?php echo form_error('first_name'); ?>
				<input id="first_name" type="text" name="first_name" maxlength="255" value="<?php echo set_value('first_name'); ?>"  /><span>*</span>
			  </li>
              <li>
				<?php echo form_error('last_name'); ?>
				<input id="last_name" type="text" name="last_name" maxlength="255" value="<?php echo set_value('last_name'); ?>"  /><span>*</span>
			  </li>
              <li>
				<?php echo form_error('email'); ?>
				<input id="email" type="text" name="email" maxlength="255" value="<?php echo set_value('email'); ?>"  /><span>*</span>
			  </li>
              <li class="pic">
				<?php echo form_error('user_image'); ?>
				<div class="img-wrapper"><img src="<?php echo $user_profile_picture.'?type=normal';?>" /></div>
				<div class="upload-pic">
					<span><a href="#">(Change)</a></span>
					<input id="user_image" type="file" name="user_image" />
				</div>
			  </li>
            </ul>
            <h2>Company information</h2>
            <ul class="form">
              <li>
				<?php echo form_error('company_name'); ?>
				<input id="company_name" type="text" name="company_name" maxlength="255" value="<?php echo set_value('company_name'); ?>"  /><span>*</span>
			  </li>
              <li>
			    <?php echo form_error('company_detail'); ?>
				<textarea id="company_detail" name="company_detail"><?php echo set_value('company_detail'); ?></textarea>
			  </li>
              <li class="pic">
				<?php echo form_error('company_image'); ?>
				<img src="images/thumb80-80-3.jpg" alt="" />
				<div class="upload-pic">
					<span><a href="#">(Change)</a></span>
					<input id="company_image" type="file" name="company_image" />
				</div>
			  </li>
            </ul>
            <p>
				<a class="bt-continue" href="#"><span>Continue</span></a>
			</p>
            <?php 
			echo form_submit('signup-form', 'Submit', 'style="display:none"');
			echo form_close(); ?>