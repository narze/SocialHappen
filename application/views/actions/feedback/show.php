<input type="hidden" id="action_data_id" value="<?php echo $action_data_id; ?>" />
<input type="hidden" id="feedbacks_per_page" value="5" />
<div class="container feedbacks-list">
    <?php
      if(count($feedbacks)) {
        foreach ($feedbacks as $feedback): ?>
            <div class="row">
              <div class="span8">
                <div class="row-fluid">
                  <div class="span1 align-center">
                    <a href="#" class="user-thumbnail">
                      <img src="<?php echo $feedback['user']['user_image'] ?>" alt="">
                    </a>
                  </div>
                  <div class="span10">
                    <p>
                      <a href="#<?php //echo base_url() . 'user/?uid=' . $feedback['user_id'];?>"><?php echo $feedback['user']['user_first_name'] . ' ' . $feedback['user']['user_last_name'] ?></a>
                      <span class="moment muted"><?php echo $feedback['user_data']['timestamp']; ?></span>
                    </p>
                    <blockquote>
                      <?php echo nl2br($feedback['user_data']['user_feedback']) ?>
                    </blockquote>
                    <ul>
                      <li>
                        Score: <?php for ($i=0; $i < $feedback['user_data']['user_score']; $i++) {
                          ?><i class="icon-star"></i><?php
                        } ?>
                      </li>
                      <li>
                        Challenge: <a target="_blank" href="<?php echo base_url() . 'player/challenge/' . $feedback['challenge']['hash'];?>"><?php echo $feedback['challenge']['detail']['name'] ?></a>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </div> <hr /><?php
        endforeach;
      }
    ?>
</div>

<div id="feedbacks_pagination"></div>