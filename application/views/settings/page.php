<div id="facebook-page-information">
<?php if(isset($success)==TRUE) echo 'Updated'; ?>
	<h2><span>Facebook page information</span></h2>
	<div>
	  <ul class="form01">
		<li><strong>Page URL:</strong><a href="<?php echo $page_facebook['link'];?>"><?php echo $page_facebook['name'];?></a></li>
		<li><strong>Page status :</strong><?php echo $page['page_status_name'];?></li>
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
                    <p class="pic"><img src="<?php echo imgsize($page['page_image'],'square');?>" /></p>
                    <p><a class="bt-change_pic" href="#"><input id="page_image" type="file" name="page_image" style="opacity: 0; height: 30px; "/><span>Change picture</span></a></p>
					<p>
						<input type="checkbox" id="use_facebook_picture" name="use_facebook_picture" <?php echo set_checkbox('use_facebook_picture', NULL, FALSE); ?> /> 
						<label for="use_facebook_picture">use your facebook avatar</label>
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
				  <p><img src="<?php echo imgsize($user['user_image'],'normal');?>" alt="" /><a href="#"></a></p>
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
				  <p><img src="<?php echo imgsize($user['user_image'],'normal');?>" alt="" /></p>
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
	
<div id="page-application">
<?php if(isset($success)==TRUE) echo 'Updated'; ?>
<h2><span>Page applications</span></h2>
   <div class="company-app">
		<ul>
			<?php foreach($page_apps as $app): ?>
				<li>
				 <p><img alt="" src="<?php echo imgsize($app['app_image'],'normal'); ?>">
				  <span class="button">
					<a href="#" class="bt-update_app"><span>update</span></a>
					<a href="#" class="bt-setting_app"><span>Setting</span></a>
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
		  <p><a class="bt-delete_page" href="#"><span>Delete page</span></a></p>
	</div>
</div>
-->