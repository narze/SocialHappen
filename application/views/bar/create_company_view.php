<div class="popup_create-company">
<div id="create-company">
<h2>Create Company</h2>
<div id="create-company-form">
	<?php
		$attributes = array('class' => 'create-company-form', 'id' => '');
		echo form_open('bar/create_company', $attributes); ?>
		
		<div class="form">
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
				<img src="<?php echo base_url(); ?>assets/images/default/company.png" width="80" height="80" />
				<div class="upload-pic">
					<span><a href="#">(Change)</a></span>
					<input id="company_image" type="file" name="company_image" />
				</div>
			  </li>
            </ul>
			<p>
				<a class="bt-continue" href="#"><span>Continue</span></a>
				<?php echo form_submit( 'submit-form', 'Submit', 'style="display:none"'); ?> 
			</p>
			<br class="clear" />
		</div>
	<?php echo form_close(); ?>
</div>
</div>
</div>