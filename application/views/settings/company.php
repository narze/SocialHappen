
<div id="company-information"> 
	<?php if(issetor($success)==TRUE) echo 'Updated'; ?>
	<h2><span>Company information</span></h2>
		
	<?php
		$attributes = array('class' => 'company-information', 'id' => '');
		echo form_open("settings/company/{$company['company_id']}", $attributes); ?>
		 <div>
              <ul class="form01">
                <li>
                  <strong>Picture profile :</strong>
				<?php echo form_error('company_image'); ?>
				  <div class="pic-profile bg-none">
                    <p class="pic"><img class="company-image" src="<?php echo imgsize($company['company_image'],'square');?>" /></p>
                    <p>
						<input id="company_image" type="file" name="company_image" style="opacity:0;filter: Alpha(Opacity=0);height:29px;position: absolute;width: 144px; "/>
						<a class="bt-change_pic" href="#"><span>Change picture</span></a>
					</p>
                  </div>
                </li>
                <li><strong>Company name :</strong><?php echo form_error('company_name'); ?><input id="company_name" type="text" name="company_name" maxlength="255" value="<?php echo html_entity_decode(set_value('company_name',$company['company_name'])); ?>"  /></li>
                <li><strong>Company detail :</strong><?php echo form_error('company_detail'); ?><?php echo form_textarea( array( 'name' => 'company_detail', 'id' => 'company_detail' , 'cols'=> 30 ,'value' => html_entity_decode(set_value('company_detail',$company['company_detail']) ) ) ); ?></li>
                <li><strong>Company email :</strong><?php echo form_error('company_email'); ?><input id="company_email" type="text" name="company_email" maxlength="255" value="<?php echo set_value('company_email',$company['company_email']); ?>"  /></li>
                <li><strong>Company telephone number :</strong><?php echo form_error('company_telephone'); ?><input id="company_telephone" type="text" name="company_telephone" maxlength="255" value="<?php echo set_value('company_telephone',$company['company_telephone']); ?>"  /></li>
                <li><strong>Company website :</strong><?php echo form_error('company_website'); ?><input id="company_website" type="text" name="company_website" maxlength="255" value="<?php echo set_value('company_website',$company['company_website']); ?>"  /></li>
                <li><?php echo form_submit('submitForm', 'Submit', 'class="bt-update"'); ?></li>
              </ul> 
            </div>
	<?php echo form_close(); ?>
	</div>
<!--
	<div id="company-admin">
	<?php if(issetor($success)==TRUE) echo 'Updated'; ?>
	<h2><span>Company admins</span></h2>
	<div class="admin-list">
		<ul>
			<?php foreach($company_users as $user): ?>
				<li class="<?php echo strtolower($user['user_role_name']);?>">
				  <p><img class="user-image" src="<?php echo imgsize($user['user_image'],'normal');?>" alt="" /></p>
				  <p><b><?php echo $user['user_first_name']; ?></b><span><?php if($role = issetor($user['user_role_name'])) echo "({$role})";?></span></p>
				</li>
			<?php endforeach; ?>
		  </ul>
		  <p class="popup">Add admin by user id
			<?php
				$attributes = array('class' => 'company-admin', 'id' => '');
				echo form_open("settings/company_admin/{$company['company_id']}", $attributes); ?>
				 <ul class="form01">
					<li><strong>User id :</strong><?php echo form_error('user_id'); ?><input id="user_id" type="text" name="user_id" maxlength="20" value="<?php echo set_value('user_id'); ?>"  /></li>
					<li><?php echo form_submit('submitForm', 'Submit', 'class="bt-update"'); ?></li>
				</ul> 
				<?php echo form_close(); ?>
		  </p>
		</div>
	</div>
-->
	<div id="company-application">
	<?php if(issetor($success)==TRUE) echo 'Updated'; ?>
	<h2><span>Company Applications</span></h2>
		<div class="company-app">
			<ul>
				<?php foreach($company_apps as $app): ?>
					<li>
					 <p><img alt="" class="app-image" src="<?php echo imgsize($app['app_image'],'normal');; ?>">
                      <span class="button">
                        <a class="bt-update_app"><span>update</span></a>
                        <a class="bt-setting_app"><span>Setting</span></a>
                      </span>
                     </p>
                    <p><?php echo $app['app_name']?></p>
					</li>
				<?php endforeach; ?>
		    </ul>
        </div>
	</div>
	
	<!--<div id="delete-company" class="style01">
		<h2><span>Delete Company</span></h2>
		<div>
			  <p>Text tell user what happen when delete company</p>
			  <p>and if user have a problem he can contact our support or see FAQs</p>
			  <p><a class="bt-delete_company-1"><span>Close account</span></a></p>
		</div>
    </div>-->
</div>
