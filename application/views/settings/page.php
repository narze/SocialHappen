<div id="facebook-page-information">
<?php if(isset($success)==TRUE) echo 'Updated'; ?>
	<h2><span>Facebook page information</span></h2>
	<div>
	  <ul class="form01">
		<li><strong>Page URL:</strong><a href="<?php echo $page_facebook['link'];?>"><?php echo $page_facebook['name'];?></a></li>
		<li><strong>Page status :</strong><?php echo $page['page_status'];?></li>
	  </ul>
	</div>
</div>

<div id="page-information"> 
<?php if(isset($success)==TRUE) echo 'Updated'; ?>
	<h2><span>Page information</span></h2>
		
<?php // Change the css classes to suit your needs   
		$attributes = array('class' => 'page-information', 'id' => '');
		echo form_open("settings/page/{$page['page_id']}", $attributes); ?>
		 <div>
              <ul class="form01">
                <li>
                  <strong>Picture profile :</strong>
				<?php echo form_error('page_image'); ?>
				   <div class="pic-profile">
                    <p class="pic"><img class="page-image" src="<?php echo imgsize($page['page_image'],'square');?>" /></p>
                    <p>
						<input id="page_image" type="file" name="page_image" style="opacity:0;filter: Alpha(Opacity=0);height:29px;position: absolute;width: 144px; "/>
						<a class="bt-change_pic" href="#"><span>Change picture</span></a>
					</p>
					<p>
						<input type="checkbox" id="use_facebook_picture" name="use_facebook_picture" <?php echo set_checkbox('use_facebook_picture', NULL, FALSE); ?>> 
						<label for="use_facebook_picture">use your facebook page avatar</label>
					</p> 
                  </div>
                </li>
                <li><strong>page name :</strong><?php echo form_error('page_name'); ?><input id="page_name" type="text" name="page_name" maxlength="255" value="<?php echo set_value('page_name',$page['page_name']); ?>"  /></li>
                <li><strong>page detail :</strong><?php echo form_error('page_detail'); ?><?php echo form_textarea( array( 'name' => 'page_detail', 'id' => 'page_detail' , 'cols'=> 30 ,'value' => set_value('page_detail',$page['page_detail']) ) ); ?></li>
                <li><?php echo form_submit('submitForm', 'Submit', 'class="bt-update"'); ?></li>
              </ul> 
            </div>
	<?php echo form_close(); ?>
</div>
	
<!--
<div id="page-admin">
	<?php if(isset($success)==TRUE) echo 'Updated'; ?>
	<h2><span>Page admins</span></h2>
	<?php // Change the css classes to suit your needs   
	$attributes = array('class' => 'page-admin', 'id' => '');
	echo form_open("settings/page_admin/{$page['page_id']}", $attributes); ?>
	<div class="admin-list">
		<ul>
			<?php foreach($company_users as $user): 
				$role = issetor($user['page_user_role_name']);?>
				<li class="<?php echo strtolower($role);?>">
				  <p><img class="user-image" class="user-image" src="<?php echo imgsize($user['user_image'],'normal');?>" alt="" /><a></a></p>
				  <p>
					<input type="checkbox" name="page_admin[]" class="page_admin" value=<?php echo $user['user_id'];?> <?php echo set_checkbox('page_admin', $user['user_id'], issetor($user['page_user_role_id'])!=''); ?> />
				  </p>
				</li>
			<?php endforeach; ?>
		</ul>
		<div><?php echo form_submit('submitForm', 'Submit', 'class="bt-update"'); ?></div>
	</div>
	<?php echo form_close(); ?>
</div>
-->
<!--
<div id="admin-admin">
	<?php if(issetor($success)==TRUE) echo 'Updated'; ?>
	<h2><span>Page admins</span></h2>
	<div class="admin-list">
		<ul> 
			<?php foreach($company_users as $user): ?>
				<li class="<?php echo strtolower($user['user_role_name']);?>">
				  <p><img src="<?php echo imgsize($user['user_image'],'normal');?>" alt="" /></p>
				  <p><b><?php echo $user['user_first_name']; ?></b><span><?php if($role = issetor($user['user_role_name'])) echo "({$role})";?></span></p>
				</li>
			<?php endforeach; ?>
			<?php foreach($page_users as $user): ?>
				<li class="<?php echo strtolower($user['user_role_name']);?>">
				  <p><img class="user-image" src="<?php echo imgsize($user['user_image'],'normal');?>" alt="" /></p>
				  <p><b><?php echo $user['user_first_name']; ?></b><span><?php if($role = issetor($user['user_role_name'])) echo "({$role})";?></span></p>
				</li>
			<?php endforeach; ?>
		  </ul>
		  <p class="popup">Add admin by user id
			<?php
				$attributes = array('class' => 'page-admin', 'id' => '');
				echo form_open("settings/page_admin/{$page['page_id']}", $attributes); ?>
				 <ul class="form01">
					<li><strong>User id :</strong><?php echo form_error('user_id'); ?><input id="user_id" type="text" name="user_id" maxlength="20" value="<?php echo set_value('user_id'); ?>"  /></li>
					<li><?php echo form_submit('submitForm', 'Submit', 'class="bt-update"'); ?></li>
				</ul> 
				<?php echo form_close(); ?>
		  </p>
		</div>
	</div>
-->

<div id="page-user-fields">
	<?php if(isset($success)==TRUE) echo 'Updated'; ?>
	<h2><span>Page user fields</span></h2>
	<?php 
	$attributes = array('class' => 'page-user-fields', 'id' => '');
	echo form_open("settings/page_user_fields/{$page['page_id']}", $attributes); ?>
	<div class="field-list">
		<?php if(isset($page_user_fields)) :
			$nth = 0;
			foreach($page_user_fields as $id => $field) : ?>
				<?php echo $id; ?>
				<input type="hidden" name="id[]" value="<?php echo $id;?>" />
				<p>
						<label for="name">Name <span class="required">*</span></label>
						<?php echo form_error('edit_name[]'); ?>
						<input type="text" name="edit_name[]" maxlength="255" value="<?php echo set_value('edit_name[]', issetor($field['name'])); ?>"  />
				</p>

				<p>
						<label for="label">Label <span class="required">*</span></label>
						<?php echo form_error('edit_label[]'); ?>
						<input type="text" name="edit_label[]" maxlength="255" value="<?php echo set_value('edit_label[]',  issetor($field['label'])); ?>"  />
				</p>

				<p>
						<label for="type">Type <span class="required">*</span></label>
						<?php echo form_error('type'); ?>
						
						<?php $options = array(
						  ''  => 'Select Type',
						  'text'    => 'Text',
						  'textarea' => 'Textarea',
						  'checkbox' => 'Checkbox',
						  'radio' => 'Radio'
						); ?>

						<?php echo form_dropdown('edit_type[]', $options, set_value('edit_type[]',  issetor($field['type'])))?>
				</p>                                             
											
				<p>
					
						<?php echo form_error('edit_required[]'); ?>
						
						<?php // Change the values/css classes to suit your needs ?>
						<input type="checkbox" name="edit_required[]" value="<?php echo $nth;?>" class="" <?php echo set_checkbox('edit_required[]', $nth, issetor($field['required']) != ''); ?>> 
								   
					<label for="required">Required</label>
				</p> 

<!-- next release
				<p>
					
						<?php echo form_error('edit_rules[]'); ?>
						
						<?php // Change the values/css classes to suit your needs ?>
						<input type="checkbox" name="edit_rules[]" value="rules" class="" <?php echo set_checkbox('edit_rules[]', 'rules'); ?>> 
								   
					<label for="rules">Rules</label>
				</p>
-->				
				<p>
						<label for="items">Items</label>
						<?php echo form_error('edit_items[]'); ?>
						<?php if(is_array(issetor($field['items']))) : ?>
							<input type="text" name="edit_items[]"  value="<?php echo set_value('edit_items[]',  implode(',',$field['items'])); ?>" />
						<?php else :?>
							<input type="text" name="edit_items[]"  value="<?php echo set_value('edit_items[]'); ?>" />
						<?php endif; ?>
				</p>

				<p>
						<label for="order">Order <span class="required">*</span></label>
						<?php echo form_error('edit_order[]'); ?>
						<input type="text" name="edit_order[]" maxlength="5" value="<?php echo set_value('edit_order[]', issetor($field['order'])); ?>"  />
				</p>

				[Button] remove this field!
				<hr />
			<?php $nth++;
			endforeach;
		endif;
		?>
		add new field
		<p>
						<label for="name">Name <span class="required">*</span></label>
						<?php echo form_error('name'); ?>
						<input type="text" name="name" maxlength="255" value="<?php echo set_value('name'); ?>"  />
				</p>

				<p>
						<label for="label">Label <span class="required">*</span></label>
						<?php echo form_error('label'); ?>
						<input type="text" name="label" maxlength="255" value="<?php echo set_value('label'); ?>"  />
				</p>

				<p>
						<label for="type">Type <span class="required">*</span></label>
						<?php echo form_error('type'); ?>
						
						<?php // Change the values in this array to populate your dropdown as required ?>
						<?php $options = array(
						  ''  => 'Select Type',
						  'text'    => 'Text',
						  'textarea' => 'Textarea',
						  'checkbox' => 'Checkbox',
						  'radio' => 'Radio'
						); ?>

						<?php echo form_dropdown('type', $options, set_value('type'))?>
				</p>                                             
											
				<p>
					
						<?php echo form_error('required'); ?>
						
						<?php // Change the values/css classes to suit your needs ?>
						<input type="checkbox" name="required" value="1" class="" <?php echo set_checkbox('required', 1); ?>> 
								   
					<label for="required">Required</label>
				</p> 

<!-- next release
				<p>
					
						<?php echo form_error('rules'); ?>
						
						<?php // Change the values/css classes to suit your needs ?>
						<input type="checkbox" name="rules" value="enter_value_here" class="" <?php echo set_checkbox('rules'); ?>> 
								   
					<label for="rules">Rules</label>
				</p>
-->				
				<p>
						<label for="items">Items</label>
						<?php echo form_error('items'); ?>
						<input type="text" name="items" value="<?php echo set_value('items'); ?>"  />
				</p>

				<p>
						<label for="order">Order <span class="required">*</span></label>
						<?php echo form_error('order'); ?>
						<input type="text" name="order" maxlength="5" value="<?php echo set_value('order'); ?>"  />
				</p>
	</div>
		
		<div><?php echo form_submit('submitForm', 'Submit', 'class="bt-update"'); ?></div>
	
	<?php echo form_close(); ?>
</div>

<div id="page-application">
<?php if(isset($success)==TRUE) echo 'Updated'; ?>
<h2><span>Page applications</span></h2>
   <div class="company-app">
		<ul>
			<?php foreach($page_apps as $app): ?>
				<li>
				 <p><img alt="" class="app-image" src="<?php echo imgsize($app['app_image'],'normal'); ?>">
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
<!--
<div id="delete-page" class="style01">
	<h2><span>Delete Page</span></h2>
	<div>
		  <p>Text tell user what happen when delete page</p>
		  <p>and if user have a problem he can contact our support or see FAQs</p>
		  <p><a class="bt-delete_page"><span>Delete page</span></a></p>
	</div>
</div>
-->