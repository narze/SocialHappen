 <div class="wrapper-details campaigns">
        <h2 class="campaign"><span>Campaign</span></h2>
		<?php if($campaign_count > 0) { ?>
        <div class="filter">
          <ul>
			      <li class="title">filter by :</li>
            <li class="active"><a class="campaign-filter" data-filter="">All</a></li>
            <li><a class="campaign-filter" data-filter="incoming">Incoming</a></li>
            <li><a class="campaign-filter" data-filter="active">Active</a></li>
            <li><a class="campaign-filter" data-filter="expired">Expired</a></li>
          </ul>
        </div>
        <div class="details campaigns">
          <table cellpadding="0" cellspacing="0">
            <tr class="campaign-row hidden-template">
              <td class="app-list">
                <div class="detail-list-style01">
                  <p class="thumb"><img class="campaign-image" src="<?php echo base_url()."assets/images/default/campaign.png";?>"></p>
                  <h2></h2>
                  <p class="description"></p>
                </div>
              </td>
              <td class="status2 campaign-status">Status <span></span></td>
              <td class="status2 campaign-visitor">Total visitors <b></b></td>
              <td class="status2 campaign-member">Total members <b></b></td>
              <td class="status2 remaining-days">Remaining days <b></b></td>
              <td class="bt-icon-cam"><a class="bt-go campaigns" href="#"><span>go</span></a></td>
            </tr>
          </table>
		  <div class="pagination-campaigns strip"></div>
        </div>
		<?php } else { ?>
		<div class="blank-tab white-box-01" style="background-image: url(<?php echo base_url(); ?>assets/images/bg/blank_campaign.jpg);">
			<h2>There's no campaign yet.</h2>
			<p class="sub-title">What's campaign?</p>
			<div class="new-item"><a class="bt-addnew_campaign" href="<?php echo base_url().'settings/page_apps/'.$page_id; ?>"><span>Add new campaign</span></a></div>
			<hr />
			<h3>Why do I have to create a campaign?</h3>
			<ul>
				<li>Tell users why thay have to create a campaign on the page.</li>
				<li>At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis</li>
			</ul>
			<h3>Online Marketing Solution</h3>
			<ul>
				<li>Jett vero eos et accusamus et iusto odio dignissimos bolone qui blanditiis</li>
			</ul>
			<a class="bt-learn-more-campaign" href="<?php echo base_url('home/package').''; ?>">Learn more about campaign</a>
		</div>
		<?php } ?>
		<br />
</div>
