<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Add new user role</title>
	<?php echo link_tag('css/style.css'); ?>
</head>
<body>
<h1>Add new user role</h1>
<?php   

$attributes = array('class' => '', 'id' => '');
echo form_open('backend/add_new_user_role', $attributes); ?>

<p>
    <label for="user_role_name">User Role Name <span class="required">*</span></label>
    <?php echo form_error('user_role_name'); ?>
    <br /><input id="user_role_name" type="text" name="user_role_name" maxlength="255" value="<?php echo set_value('user_role_name'); ?>"  />
</p>

<p>
    <?php echo form_error('role_all'); ?>
    <br /><input type="checkbox" id="role_all" name="role_all" value="1" class="" <?php echo set_checkbox('role_all', '1'); ?>>                  
	<label for="role_all">Role All</label>
</p> 

<p>
    <?php echo form_submit( 'submit', 'Submit'); ?>
</p>

<?php echo form_close(); ?>

</body>
</html>