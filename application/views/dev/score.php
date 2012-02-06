<?php // Change the css classes to suit your needs    
if($success){
        echo 'Updated';
}
$attributes = array('class' => '', 'id' => '');
echo form_open('dev/score', $attributes); ?>
<p>
        <label for="user_id">User_id <span class="required">*</span></label>
        <?php echo form_error('user_id'); ?>
        <br /><input id="user_id" type="text" name="user_id" maxlength="10" value="<?php echo set_value('user_id', $user_id); ?>"  />
</p>

<p>
        <label for="page_id">Page_id <span class="required">*</span></label>
        <?php echo form_error('page_id'); ?>
        <br /><input id="page_id" type="text" name="page_id" maxlength="10" value="<?php echo set_value('page_id'); ?>"  />
</p>

<p>
        <label for="score">Score <span class="required">*</span></label>
        <?php echo form_error('score'); ?>
        <br /><input id="score" type="text" name="score" maxlength="10" value="<?php echo set_value('score'); ?>"  />
</p>

<p>     <?php foreach($page_scores as $page_score) {
                echo 'Page : '.$page_score['page_id'].' -> '.$page_score['score'].'<br />';
        } ?>
</p>

<p>
        <?php echo form_submit( 'submit', 'Submit'); ?>
</p>

<?php echo form_close(); ?>