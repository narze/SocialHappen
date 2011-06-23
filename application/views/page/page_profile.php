<div class="box-information">
        <div class="details">
          <div class="pic">
            <p><img src="<?php echo issetor($page_profile['page_image']); ?>" alt=""></p>
            <p><a class="bt-go_page" href="<?php echo $facebook['link'];?>"><span>Goto Page</span></a></p>
            <p><a class="bt-add_app" href="#"><span>Add App</span></a></p>
          </div>
          <h2><?php echo issetor($page_profile['page_name']); ?></h2>
          <p><?php echo issetor($page_profile['page_detail']); ?></p>
        </div>
        <div class="information">
          <h2>Information</h2>
          <ul>
            <li><span>New Member</span><?php echo $user_count;?></li>
            <li><span>All Member</span>----</li>
            <li><span>Like</span><?php echo $facebook['likes'];?></li>
            <li><span>Application</span><?php echo $app_count;?></li>
            <li><span>Campaign</span><?php echo $campaign_count;?></li>
          </ul>
        </div>
</div>