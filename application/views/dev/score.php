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
        <label for="company_id">company_id <span class="required">*</span></label>
        <?php echo form_error('company_id'); ?>
        <br /><input id="company_id" type="text" name="company_id" maxlength="10" value="<?php echo set_value('company_id'); ?>"  />
</p>

<p>
        <label for="score">Score <span class="required">*</span></label>
        <?php echo form_error('score'); ?>
        <br /><input id="score" type="text" name="score" maxlength="10" value="<?php echo set_value('score'); ?>"  />
</p>

<p>     <?php foreach($company_scores as $company_score) {
                echo 'company : '.$company_score['company_id'].' -> '.$company_score['score'].'<br />';
        } ?>
</p>

<p>
        <?php echo form_submit( 'submit', 'Submit'); ?>
</p>

<?php echo form_close(); ?>