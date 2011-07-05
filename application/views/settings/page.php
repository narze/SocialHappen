<?php if(isset($success)) echo 'Updated'; ?>
	<div id="facebook-page-information">
		<h2><span>Facebook page information</span></h2>
            <div>
              <ul class="form01">
                <li><strong>Page URL:</strong><a href="<?php echo $page_facebook['link'];?>"><?php echo $page_facebook['name'];?></a></li>
                <li><strong>Page status :</strong><?php echo $page['page_status_name'];?></li>
              </ul>
            </div>
</div>

<div id="page-information"> 
	<h2><span>Page information</span></h2>
		
<?php // Change the css classes to suit your needs   
		$attributes = array('class' => 'page', 'id' => '');
		echo form_open("settings/page/{$page['page_id']}", $attributes); ?>
		 <div>
              <ul class="form01">
                <li>
                  <strong>Picture profile :</strong>
				<?php echo form_error('page_image'); ?>
                  <div class="pic-profile">
                    <p class="pic"><img src="<?php echo imgsize($page['page_image'],'square');?>" /></p>
                    <p><a class="bt-change_pic" href="#"><span>Change picture</span><input id="page_image" type="file" name="page_image" style="opacity: 0; height: 30px; "/></a></p>
                  </div>
                </li>
                <li><strong>page name :</strong><?php echo form_error('page_name'); ?><input id="page_name" type="text" name="page_name" maxlength="255" value="<?php echo set_value('page_name',$page['page_name']); ?>"  /></li>
                <li><strong>page detail :</strong><?php echo form_error('page_detail'); ?><?php echo form_textarea( array( 'name' => 'page_detail', 'id' => 'page_detail' , 'cols'=> 30 ,'value' => set_value('page_detail',$page['page_detail']) ) ); ?></li>
                <li><?php echo form_submit('submitForm', 'Submit', 'class="bt-update"'); ?></li>
              </ul> 
            </div>
	<?php echo form_close(); ?>
	</div>
	
	<div id="page-application">
	<h2><span>Page applications</span></h2>
        <?php foreach($page_apps as $app): ?>
			<div><?php echo $app['app_name']?></div>
		<?php endforeach; ?>
	</div>
	
	<div id="delete-page" class="style01">
		<h2><span>Delete Page</span></h2>
		<div>
			  <p>Text tell user what happen when delete page</p>
			  <p>and if user have a problem he can contact our support or see FAQs</p>
			  <p><a class="bt-delete-page" href="#"><span>Delete page</span></a></p>
		</div>
    </div>
</div>
