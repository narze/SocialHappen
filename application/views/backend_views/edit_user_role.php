<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Addt user role</title>
	<?php echo link_tag('css/style.css'); ?>
</head>
<body>
<h1>Edit user role</h1>
<?php   

$attributes = array('class' => '', 'id' => '');
echo form_open('backend/edit_user_role/'.$user_role_id, $attributes); ?>

<p>
    <label for="user_role_name">User Role Name <span class="required">*</span></label>
    <?php echo form_error('user_role_name'); ?>
    <br /><input id="user_role_name" type="text" name="user_role_name" maxlength="255" value="<?php echo set_value('user_role_name', $user_role['user_role_name']); ?>"  />
</p>

<?php foreach($fields as $id => $field) : if($field === 'user_role_id' || $field === 'user_role_name') {continue;};?>
    <p>
        <?php echo form_error($field); ?>
        <br /><input type="checkbox" id="<?php echo $field;?>" name="<?php echo $field;?>" value="1" class="" <?php echo set_checkbox('<?php echo $field;?>', '1', $user_role[$field] ? TRUE:FALSE); ?>>                  
        <label for="<?php echo $field;?>">
            <?php 
                $words = explode('_', strtolower($field));
                foreach ($words as $word) {
                echo ucfirst($word).' ';
            } ?>
        </label>
    </p> 
<?php endforeach; ?>

<p>
    <?php echo form_submit( 'submit', 'Submit'); ?>
</p>

<?php echo form_close(); ?>

</body>
</html>