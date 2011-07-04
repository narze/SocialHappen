<?php if(isset($success)) echo 'Updated'; ?>
<div id="company-information"> 
	<h2><span>Company information</span></h2>
		
<?php // Change the css classes to suit your needs   
		$attributes = array('class' => 'company', 'id' => '');
		echo form_open("settings/company/{$company['company_id']}", $attributes); ?>
		 <div>
              <ul class="form01">
                <li>
                  <strong>Picture profile :</strong>
				<?php echo form_error('company_image'); ?>
                  <div class="pic-profile">
                    <p class="pic"><img src="<?php echo imgsize($company['company_image'],'square');?>" /></p>
                    <p><a class="bt-change_pic" href="#"><span>Change picture</span><input id="company_image" type="file" name="company_image" style="opacity: 0; height: 30px; "/></a></p>
                  </div>
                </li>
                <li><strong>Company name :</strong><?php echo form_error('company_name'); ?><input id="company_name" type="text" name="company_name" maxlength="255" value="<?php echo set_value('company_name',$company['company_name']); ?>"  /></li>
                <li><strong>Company detail :</strong><?php echo form_error('company_detail'); ?><?php echo form_textarea( array( 'name' => 'company_detail', 'id' => 'company_detail' , 'cols'=> 30 ,'value' => set_value('company_detail',$company['company_detail']) ) ); ?></li>
                <li><strong>Company name :</strong><?php echo form_error('company_email'); ?><input id="company_email" type="text" name="company_email" maxlength="255" value="<?php echo set_value('company_email',$company['company_email']); ?>"  /></li>
                <li><strong>Company telephone number :</strong><?php echo form_error('company_telephone'); ?><input id="company_telephone" type="text" name="company_telephone" maxlength="255" value="<?php echo set_value('company_telephone',$company['company_telephone']); ?>"  /></li>
                <li><strong>Company website :</strong><?php echo form_error('company_website'); ?><input id="company_website" type="text" name="company_website" maxlength="255" value="<?php echo set_value('company_website',$company['company_website']); ?>"  /></li>
                <li><?php echo form_submit('submitForm', 'Submit', 'class="bt-update"'); ?></li>
              </ul> 
            </div>
	<?php echo form_close(); ?>
	</div>
	
	<div id="company-application">
	<h2><span>Company Applications</span></h2>
            
	</div>
	
	<div id="delete-company" class="style01">
		<h2><span>Delete Company</span></h2>
		<div>
			  <p>Text tell user what happen when delete company</p>
			  <p>and if user have a problem he can contact our support or see FAQs</p>
			  <p><a class="bt-delete-company" href="#"><span>Delete Company</span></a></p>
		</div>
    </div>
</div>
