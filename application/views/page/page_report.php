<div class="wrapper-details report">
	<h2 class="application"><span>Report</span></h2>
	<?php if($user_exceed_limit) { ?>
      <div class="blank-tab white-box-01" style="background-image: url('<?php echo base_url(); ?>assets/images/bg/blank_member.jpg');">
      <h2>This page member has exceed the limit.</h2>
      <p class="sub-title">Pay me :)</p>
      <hr />
      <h3>Why do I have to pay?</h3>
      <ul>
        <li>Don't ask me now</li>
      </ul>
      <br />
    </div>
    <?php } else { ?>
    	<div id="page-report"></div>
    <?php } ?>
</div>
