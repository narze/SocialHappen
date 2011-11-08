<div id="signup-fields">
	<h2><span>SocialHappen Form</span></h2>
	<ul class="fields">
		<li>
			<div class="left">
				<label class="title">First Name :</label>
				<div class="inputs"><input type="text"></div>
			</div>
		</li>
		<li>
			<div class="left">
				<label class="title">Last Name :</label>
				<div class="inputs"><input type="text"></div>
			</div>
		</li>
		<li>
			<div class="left">
				<label class="title">Email :</label>
				<div class="inputs"><input type="text"></div>
			</div>
		</li>
	</ul>
	<h2><span>Your Sign Up Form</span></h2>
	<?php echo form_open("settings/page_apps/signup_fields/{$page['page_id']}", array('class' => 'signup-fields')); 
	if($updated) echo 'Updated'; ?>
	<input type="hidden" name="submit-form" value=1></input>
	<ul class="submitting fields">
	<li class="no-field" <?php echo $signup_fields ? 'style="display:none"' : '' ;?>>to start adding more sign up field, click the button below</li>
		<?php 
		if($signup_fields) //Existing fields
		{
			foreach($signup_fields as $signup_field)
			{ 
			$key = issetor($signup_field['name']); ?>
				<li class="<?php echo $key?>">
					<div class="left">
					<label class="title"><?php echo $signup_field['label']; ?> :</label>
					<div class="inputs">
						<input class="type" type="hidden" name="<?php echo $key; ?>[type]" value="<?php echo $signup_field['type'];?>"></input>
						<input class="name" type="hidden" name="<?php echo $key; ?>[name]" value="<?php echo $key; ?>"></input>
						<input class="label" type="hidden" name="<?php echo $key; ?>[label]" value="<?php echo $signup_field['label'] ?>"></input>
					<?php 
					if(isset($signup_field['items']) && is_array($signup_field['items'])) {
						foreach ($signup_field['items'] as $item)
						{?>
							<label><input type="<?php echo $signup_field['type'];?>"> <?php echo $item;?></input></label>
							<input class="items" type="hidden" name="<?php echo $key; ?>[items][]" value="<?php echo $item; ?>"></input>
						<?php }
					} else {
						if($signup_field['type'] == 'textarea') { ?> <textarea></textarea> <?php }
						else { ?> <input type="<?php echo $signup_field['type'];?>"></input> <?php }
					} ?>
					</div>
					</div>
					<div class="right">
					<label class="required">
						<input class="required" type="checkbox" name="<?php echo $key?>[required]" value=1 <?php echo ($signup_field['required']) ? 'checked="checked"' : '';?>/> Required</label>
						<span class="separator">|</span> 
					<a class="bt-remove-field">Remove</a>
					</div>
				</li> <?php
			}
		} ?>
	</ul>
	
	<a id="bt-add-field-from-list" class="bt-add-field-from-list">Add field by choose from the list</a>
	<a class="bt-create-own-field">Create your own field</a>
	<?php 
	echo form_submit('submitForm', 'Submit', 'class="bt-update"');
	echo form_close(); ?>
	<div id="no-submit" style="display:none"><ul>
		<li class="template">
			<div class="left">
				<label class="title"> :</label>
				<div class="inputs">
					<input class="type" type="hidden"></input>
					<input class="name" type="hidden"></input>
					<input class="label" type="hidden"></input>
				</div>
			</div>
			<div class="right">
				<label class="required">
					<input class="required" type="checkbox" value=1 name="" id=""/> Required</label>
					<span class="separator">|</span> 
				<a class="bt-remove-field">Remove</a>
			</div>
		</li>
		<?php 
		if(isset($default_fields))
		{
			foreach($default_fields as $default_field)
			{ 
				$key = issetor($default_field['name']);
				if(!in_array($key, $signup_field_names)){ //for default fields that was not use, we hide it here?>
					<li class="<?php echo $key?>">
						<div class="left">
						<label class="title"><?php echo $default_field['label']; ?> :</label>
						<div class="inputs">
							<input class="type" type="hidden" name="<?php echo $key; ?>[type]" value="<?php echo $default_field['type'];?>"></input>
							<input class="name" type="hidden" name="<?php echo $key; ?>[name]" value="<?php echo $key; ?>"></input>
							<input class="label" type="hidden" name="<?php echo $key; ?>[label]" value="<?php echo $default_field['label'] ?>"></input>
						<?php 

						if(isset($default_field['items'])) {
							foreach ($default_field['items'] as $item)
							{?>
								<label><input type="<?php echo $default_field['type'];?>"> <?php echo $item;?></input></label>
								<input class="items" type="hidden" name="<?php echo $key; ?>[items][]" value="<?php echo $item; ?>"></input>
							<?php }
						} else {
							if($default_field['type'] == 'textarea') { ?> <textarea></textarea> <?php }
							else { ?> <input type="<?php echo $default_field['type'];?>"></input> <?php }
						}
						?>
						</div>
						</div>
						<div class="right">
						<label class="required">
							<input class="required" type="checkbox" value=1 name="<?php echo $key?>[required]" /> Required</label>
							<span class="separator">|</span> 
						<a class="bt-remove-field">Remove</a>
						</div>
					</li> <?php
				}
			}
		}
		?>
	</ul></div>
	
</div>


<div id="fancy" style="display:none">
	<!-- Pop up default field-->
	<div id="default-fields">
		<h2 class="in-popup">Add more field</h2>
		<p>Choose from the list below</p>
		<ul><?php
		foreach($default_fields as $default_field)
		{ ?>
			<li>
				<input type="checkbox" name="<?php echo $default_field['name']?>" />
				<label for="<?php echo $default_field['name']?>"><?php echo $default_field['label']; ?></label>
			</li><?php
		} ?>
		</ul>
		<p class="apply">
			<a class="bt-apply-to-signup-form">Add these fields to the sign up form</a>
		</p>
	</div>
	
	<!-- Pop up custom field-->
	<div id="custom-fields-template">
		<h2 class="in-popup">Create your own field</h2>
		<ul class="fields">
			<li class="field">
				<div class="left">
				<label>Field title :</label><input type="text" class="new-field" name="new-field" />
				<label>Field type :</label> 
					<select name="field-type">
						<option value='text' selected="selected">Text</option>
						<option value='textarea'>Paragraph Text</option>
						<option value='radio'>Radio Button</option>
						<option value='checkbox'>Checkbox</option>
					</select>
				</div>
				<div class="right">
				<label><input type="checkbox" value=1 name="new-field-required" /> Required</label><span class="separator">|</span> 
				<a class="bt-remove-field">Remove</a>
				</div>
				<div class="options" style="display: none;"> 
					<ul>
						<li class="option"><input type="hidden" name="option-type-example" class="option-type" /><input class="option-item" type="text" value="Option 1" /><a class="bt-remove-option">Remove option</a></li>
						<li class="add-option"><input type="hidden" class="option-type" /><input type="text" value="Click to add option" readonly="readonly" /> or add <a class="add-other">"Other"</a></li>
					</ul>
				</div>
			</li>
		</ul>
		<ul class="fields-template" style="display:none;">
			<li class="field">
				<div class="left">
				<label>Field title :</label><input type="text" class="new-field"  name="new-field" />
				<label>Field type :</label> 
					<select name="field-type">
						<option value='text' selected="selected">Text</option>
						<option value='textarea'>Paragraph Text</option>
						<option value='radio'>Radio Button</option>
						<option value='checkbox'>Checkbox</option>
					</select>
				</div>
				<div class="right">
				<label><input type="checkbox" value=1 name="new-field-required" /> Required</label><span class="separator">|</span> 
				<a class="bt-remove-field">Remove</a>
				</div>
				<div class="options" style="display: none;"> 
					<ul>
						<li class="option"><input type="hidden" name="option-type-example" class="option-type" /><input class="option-item" type="text" value="Option 1" /><a class="bt-remove-option">Remove option</a></li>
						<li class="add-option"><input type="hidden" class="option-type" /><input type="text" value="Click to add option" readonly="readonly" /> or add <a class="add-other">"Other"</a></li>
					</ul>
				</div>
				
			</li>
		</ul>
		<a class="add-more-field">+ Add more field</a>
		
		<p class="apply">
			<a class="bt-apply-to-signup-form">Add this fields the sign up form</a>
		</p>
	</div>
</div>