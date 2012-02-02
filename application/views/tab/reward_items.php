      <?php if($reward_items)
      {
        foreach ($reward_items as $reward_item) 
        { ?>      
          <div class="reward-item" data-item-id="<?php echo $reward_item['_id'];?>">
            <div class="section first">
              <div class="item-image" style="background-image:url(<?php echo $reward_item['image'] ? $reward_item['image'] : base_url().'assets/images/default/reward.png'; ?>);">
                <div class="remaining-time abs-b bold tc-blue1">Remaining Time <span class="end-time-countdown bold tc-grey5 fr"><?php echo $reward_item['end_date']; ?></span></div>
              </div>
              <ul class="item-info">
                <li class="box">
                  <span class="tc-green6 bold">User who get this reward : </span><?php 
                  if (count($reward_item['user_list'])>0) {
                    foreach ($reward_item['user_list'] as $user_id) { ?>
                      <a href="#<?php echo $user_id; ?>" title="<?php echo $user_list[$user_id]['user_first_name'].' '.$user_list[$user_id]['user_last_name']; ?>" class="user-thumb s25 inline-block mb10" style="background-image:url(<?php echo $user_list[$user_id]['user_image'] ? $user_list[$user_id]['user_image'] : base_url().'assets/images/default/user.png'; ?>);"></a><?php
                    }
                  } else { ?>
                    <p>Be the first to got this reward.</p>
                  <?php } ?>
                </li>
                <li class="box">
                  <p><span class="tc-green6 bold">Quanity: </span><?php echo $reward_item['redeem']['amount']?></p>
                  <p><span class="tc-green6 bold">Value: </span><?php echo $reward_item['value']?></p>
                  <p><span class="tc-green6 bold">Required point: </span><span class="point fs14"><?php echo $reward_item['redeem']['point']?></span></p>
                </li>
                <li><a href="" class="btn green w100 large"><span>Get this reward</span></a></li>
              </ul>
            </div>
            <div class="section bd0 mb10">
              <div class="tc-green6 fs16 bold"><?php echo $reward_item['name']?></div>
              <div class="description"><?php echo nl2br($reward_item['description']);?></div>
            </div>
          </div><?php 
        }
      } else { ?>
        <div class="no-item">No reward.</div><?php
      } ?>