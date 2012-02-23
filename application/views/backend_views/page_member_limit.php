<?php
$attributes = array('class' => '', 'id' => '');
echo form_open('backend/change_page_member_limit/'.$page_id, $attributes); ?>

<p>
        <label for="page_member_limit">Page Member limit <span class="required">*</span></label>
        <?php echo form_error('page_member_limit'); ?>
        <br /><input id="page_member_limit" type="text" name="page_member_limit"  value="<?php echo set_value('page_member_limit', $page['page_member_limit']); ?>"  />
</p>


<p>
        <?php echo form_submit( 'submit', 'Submit'); ?>
</p>

<?php echo form_close(); ?>
