<div class="popup-fb signup-page">
	
	<h2>Page Sign Up</h2>
	
    <div class="step">
	<span class="connector"></span>
	<ul>
		<li><span>1</span></li>
		<li class="active"><span>2</span></li>
		<li><span>3</span></li>
	</ul>
	</div>
	
	<div id="signup-form">
		<?php $attributes = array('class' => 'signup-form', 'id' => ''); echo form_open("tab/signup_page/{$page_id}", $attributes); ?>
        <div class="connect">
			
			<div class="wrapper">
				<div class="profile">
					<img src="<?php echo imgsize($user_profile_picture, 'square');?>" />
					<p class="name"><?php echo $user['user_first_name'];?></p>
				</div>
				
				<div class="connect-arrow"></div>
				
				<div class="profile">
					<img src="<?php echo imgsize($page['page_image'], 'square');?>" />
					<p class="name"><?php echo $page['page_name'];?></p>
				</div>
			</div>
			
			<p>Connect <b><?php echo $page['page_name']; ?> page</b> with your account.</p>
			
        </div>
		
		<?php if(isset($error)) 
		{
			echo $error; 
		}
		else 
		{ ?>
			<div class="form">
				<h2>Additional Information</h2>
				<ul>
					<input type="hidden" name="empty" value="0" /><?php 
					foreach($page_user_fields as $user_fields) 
					{ ?>
						<li <?php echo form_error($user_fields['name']) ? 'class="error"' : '' ; ?>>
							<label class="title"><?php
								if($user_fields['required']) { ?><span class="required">*</span> <?php }
								echo $user_fields['label'].' :'; ?>
							</label>
							<div class="inputs"><?php
								switch($user_fields['type'])
								{
									case 'radio':
										foreach($user_fields['items'] as $item){
											echo "<label>".form_radio($user_fields['name'], $item, set_value($user_fields['name']))." {$item}</label>";
										}
									break;
									case 'checkbox':
										$checked_list = array();
										while($checked = set_value($user_fields['name'])){
											$checked_list[] = $checked;
										}
										echo '<div class="checkbox">';
										foreach($user_fields['items'] as $item){	
											echo "<label>".form_checkbox($user_fields['name']."[]", $item, in_array($item, $checked_list))." {$item}</label>";
										}
										echo '</div>';
									break;
									case 'textarea':
										echo form_textarea(array(
											'name'        => $user_fields['name'],
											'id'          => $user_fields['name'],
											'value'		  => set_value($user_fields['name']),
											'rows'		  => '3'
										));
									break;					
									case 'text':
									default:
										echo form_input(array(
											'name'        => $user_fields['name'],
											'id'          => $user_fields['name'],
											'value'		  => set_value($user_fields['name'])
										));
								} ?>
							</div>
						</li><?php 
					} ?>
				</ul>
			  <p class="policy"><input type="checkbox" name="policy" id="policy" /> I agree to the Terms of Service. In particular, some usage information will be shared back with Facebook. For more, see our <a href="#">Privacy Policy.</a></p>
			  <div class="buttons">
				  <p class="right">
					<a class="bt-cancel"><span>Cancel</span></a>
					<a class="bt-done-inactive"><span>Done</span></a>
				  </p>
			  </div>
			</div><?php 
		} ?>
		<?php echo form_close(); ?>
    </div>
</div>