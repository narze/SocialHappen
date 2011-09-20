<div class="box-information">
        <div class="details">
          <div class="pic">
            <p><img class="page-image" src="<?php echo imgsize(issetor($page_profile['page_image']),'large'); ?>" alt=""></p>
            <p><a class="bt-go_page" href="<?php echo $facebook['link'];?>"><span>Goto Page</span></a></p>
            <?php if($page_profile['page_installed'] == 1) { ?>
			<p><a class="bt-add_app" href="#"><span>Add App</span></a></p>
			<?php } ?>
          </div>
          <h2><?php echo issetor($page_profile['page_name']); ?></h2>
          <p><?php echo issetor($page_profile['page_detail']); ?></p>
        </div>
        <div class="information">
          <h2>Information</h2>
          <ul>
            <li><span>New Member today</span><?php echo issetor($new_user_count);?></li>
            <li><span>All Member</span><?php echo $user_count;?></li>
            <li><span>Like</span><?php echo $facebook['likes'];?></li>
            <li><span>Application</span><?php echo $app_count;?></li>
            <li><span>Campaign</span><?php echo $campaign_count;?></li>
          </ul>
        </div>
</div>