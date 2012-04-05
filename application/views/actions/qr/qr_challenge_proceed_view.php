{header}
  <div class="container-fluid">

    <?php 
    if($challenge) 
    { ?>
      <div class="page-header">
        <h1 class="challenge-name"><?php echo $challenge['detail']['name'];?></h1>
      </div>

        <div class="row-fluid" id="challenge-criteria-list">
            <?php if($challenge['criteria']) 
            {
              foreach($challenge['criteria'] as $key => $criteria) : ?>
                <?php //var_export($criteria); ?>
                <p class="span1">
                  <img class="action-image" style="width:100%;" src="<?php echo isset($criteria['image']) ? $criteria['image'] : base_url('assets/images/default/action.png'); ?>" alt="<?php echo $criteria['name'];?>">
                </p>
                <h3 class="criteria-name span11"><?php 
                  if($player_logged_in && $player_challenging) : ?>
                    <a href="<?php echo base_url().'player/challenge_action/'.$challenge['hash'].'/'.$key;?>" class="criteria-link">
                      <?php echo $criteria['name']; ?>
                    </a><?php
                  elseif($player_logged_in) : ?>
                    <span>
                      <?php echo $criteria['name']; ?>
                    </span><?php 
                  endif; ?>
                </h3>

                <div class="span11 offset1"><?php
                  if($challenge_progress[$key]['action_done']) { ?>
                    <span class="badge badge-success">Done</span><?php
                  } else { ?>
                    <span class="badge"><?php echo $challenge_progress[$key]['action_count'].'/'.$criteria['count'];?></span> <?php
                  } ?>
                </div><?php 
              endforeach; 
            } ?>
        </div><?php

    } else { ?>
      <div class="alert alert-error">
        Challenge not found
      </div><?php
    } ?>
    <a href="<?php echo $proceed_url;?>">Proceed</a>
  </div>
</body>
</html>
