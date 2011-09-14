<div class="wrapper-content">
    <div class="account-data">
      <div class="pic"><img src="<?php echo imgsize($page['page_image'],'normal');?>" alt="" /><span></span></div>
      <div class="data">
        <h1><?php echo $page['page_name'];?></h1>
        <p><?php echo $page['page_detail'];?></p>
		<!--
        <ul>
          <li class="fav"><a>Add Favorite</a></li>
          <li class="member"><a><?php echo issetor($page_user_count,'-');?></a></li>
          <li class="badges"><a><?php echo issetor($page_badge_count,'-');?></a></li>
          <li class="point"><a><?php echo issetor($page_point_count,'-');?></a></li>
          <li class="setting"><a><span>setting</span></a></li>
        </ul>
		-->
      </div>
    </div>

	<div class="box-setting">

		<div id="account-information"> 
			<?php if(isset($success)) echo 'Updated'; ?>
			<h2><span>Account information</span></h2>
				
		<?php // Change the css classes to suit your needs   
				$attributes = array('class' => 'account-information', 'id' => '');
				echo form_open_multipart("tab/account/{$page['page_id']}/{$user['user_id']}", $attributes); ?>
				 <div>
					  <ul class="form01">
						<li>
						  <strong>Picture profile :</strong>
						<?php echo form_error('user_image'); ?>
						  <div class="pic-profile">
                    <p class="pic"><img src="<?php echo imgsize($user['user_image'],'square');?>" /></p>
                    <p><a class="bt-change_pic"><input id="user_image" type="file" name="user_image" style="opacity: 0; height: 30px; "/><span>Change picture</span></a></p>
					<p>
						<input type="checkbox" id="use_facebook_picture" name="use_facebook_picture" <?php echo set_checkbox('use_facebook_picture', NULL, FALSE); ?>> 
						<label for="use_facebook_picture">use your facebook avatar</label>
					</p> 
                  </div>
						</li>
						<li><strong>First name :</strong><?php echo form_error('first_name'); ?><input id="first_name" type="text" name="first_name" maxlength="255" value="<?php echo set_value('first_name',$user['user_first_name']); ?>"  /></li>
						<li><strong>Last name :</strong><?php echo form_error('last_name'); ?><input id="last_name" type="text" name="last_name" maxlength="255" value="<?php echo set_value('last_name',$user['user_last_name']); ?>"  /></li>
						<li><strong>Gender :</strong>
							<?php echo form_error('gender'); ?>
							<input id="gender" name="gender" type="radio" class="" value="3" <?php echo $this->form_validation->set_radio('gender', 3 ,3 == $user['user_gender']); ?> />
							<label for="gender" class="">Male</label>
							<input id="gender" name="gender" type="radio" class="" value="2" <?php echo $this->form_validation->set_radio('gender', 2, 2 == $user['user_gender']); ?> />
							<label for="gender" class="">Female</label>
							<input id="gender" name="gender" type="radio" class="" value="1" <?php echo $this->form_validation->set_radio('gender', 1, 1 == $user['user_gender']); ?> />
							<label for="gender" class="">Not sure</label>
						</li>
						<li><strong>Birth date :</strong><?php echo form_error('birth_date'); ?><input class="date" id="birth_date" type="text" name="birth_date" maxlength="255" value="<?php echo set_value('birth_date',$user['user_birth_date']); ?>"  /></li>
						<li><strong>About me :</strong><?php echo form_error('about'); ?><?php echo form_textarea( array( 'name' => 'about', 'id' => 'about' , 'cols'=> 30 ,'value' => set_value('about',$user['user_about']) ) ); ?></li>
						<li><?php echo form_submit('submitForm', 'Submit', 'class="bt-update"'); ?></input></li>
					  </ul> 
					</div>
			
		<?php echo form_close(); ?>
		</div>

		<!--
		<div id="sharing-information">
		<?php if(isset($success)) echo 'Updated'; ?>
		<h2><span>Sharing Information</span></h2>
				<form>
				  <div>
					<ul class="form01">
					  <li>
						<strong>Facebook :</strong><a href="<?php echo $user_facebook['link'];?>"><?php echo $user_facebook['name'];?></a>
						<div></div>
					  </li>
					  <li>
						<strong>Twitter :</strong> [twitter_name]
						<div></div>
					  </li>
					  <li>
						<strong>Thumblr :</strong> [not connected]
						<div><a>set up</a></div>
					  </li>
					</ul>
				  </div>
				</form>
		</div>

		<div id="email-notification">
			<?php if(isset($success)) echo 'Updated'; ?>
			<h2><span>E-mail Notification</span></h2>
				<form>
				  <div>
					<p class="head"><span>Group</span><span>Details</span><span>Allow</span></p>
					<ul class="form01">
					  <li class="border">
						<strong>Member notify :</strong>
						<ul class="style01">
						  <li>friend notify message <input type="checkbox" /></li>
						  <li>page notify message <input type="checkbox" /></li>
						  <li>application notify message <input type="checkbox" /></li>
						  <li>campaign notify message <input type="checkbox" /></li>
						  <li>alert new campaign <input type="checkbox" /></li>
						  <li>alert new application <input type="checkbox" /></li>
						</ul>
					  </li>
					  <li class="border">
						<strong>Admin notify :</strong>
						<ul class="style01">
						  <li>monthly report<input type="checkbox" /></li>
						  <li>weekly report<input type="checkbox" /></li>
						</ul>
					  </li>
					  <li><input class="bt-update" type="submit" /></li>
					</ul>
				  </div>
				</form></div>
				
	    <div id="close-account" class="style01">
			<h2><span>Close Account</span></h2>
			<div>
			  <p>Text tell user what happen when close account</p>
			  <p>and if user have a problem he can contact our support or see FAQs</p>
			  <p><a class="bt-close_account"><span>Close account</span></a></p>
			</div>
		  </div>-->
	</div>
</div>
