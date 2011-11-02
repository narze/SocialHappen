<div id="authorize-information">
	<?php if(isset($success)) echo 'Updated'; ?>
	<h2><span>Authorize information</span></h2>
	<div>
	  <ul class="form01">
		<li><strong>Facebook ID:</strong><a href="<?php echo $user_facebook['link'];?>"><?php echo $user_facebook['name'];?></a></li>
		<li><strong>Joined since :</strong><?php echo $user['user_register_date'];?></li>
		<li><strong>Last Active :</strong><?php echo $user['user_last_seen'];?></li>
	  </ul>
	</div>
</div>

<div id="account-information"> 
	<?php if(issetor($success)) { ?>
	<div class="notice success">Updated<a class="close"></a></div>
	<?php } ?>
	<h2><span>Account information</span></h2>
		
<?php // Change the css classes to suit your needs   
		$attributes = array('class' => 'account-information', 'id' => '');
		echo form_open_multipart("o_setting/account/{$user['user_id']}", $attributes); ?>
		 <div>
              <ul class="form01">
                <li>
                  <strong>Picture profile :</strong>
				<?php echo form_error('user_image'); ?>
                  <div class="pic-profile">
                    <p class="pic"><img class="user-image" src="<?php echo imgsize($user['user_image'],'square');?>" /></p>
                    <p>
						<input id="user_image" type="file" name="user_image" style="opacity:0;filter: Alpha(Opacity=0);height:29px;position: absolute;width: 144px; "/>
						<a class="bt-change_pic" href="#"><span>Change picture</span></a>
					</p>
					<p>
						<input type="checkbox" id="use_facebook_picture" name="use_facebook_picture" <?php echo set_checkbox('use_facebook_picture', NULL, FALSE); ?>> 
						<label for="use_facebook_picture">use your facebook avatar</label>
					</p> 
                  </div>
                </li>
                <li><strong>First name :</strong><?php echo form_error('first_name'); ?><input id="first_name" type="text" name="first_name" maxlength="255" value="<?php echo set_value('first_name',$user['user_first_name']); ?>"  /></li>
                <li><strong>Last name :</strong><?php echo form_error('last_name'); ?><input id="last_name" type="text" name="last_name" maxlength="255" value="<?php echo set_value('last_name',$user['user_last_name']); ?>"  /></li>
                <li><strong>Email :</strong><?php echo $user['user_email']; ?></li>
				<li><strong>About me :</strong><?php echo form_error('about'); ?><?php echo form_textarea( array( 'name' => 'about', 'id' => 'about' , 'cols'=> 30 ,'value' => html_entity_decode(set_value('about',$user['user_about'])) ) ); ?></li>
                <li><?php echo form_submit('submitForm', 'Submit', 'class="bt-update"'); ?></li>
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
				<div><a href="#">set up</a></div>
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
		  <p><a class="bt-close_account" href="#"><span>Close account</span></a></p>
		</div>
	  </div>
</div>
-->
