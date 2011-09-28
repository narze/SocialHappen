<div class="wrapper-details-member users">
        <h2 class="member"><span>Member</span></h2>
		<?php if($user_count > 0) { ?>
		<div class="option">
          <form>
			<p class="search"><input name="" type="text" value="search" /><input class="bt-search" type="submit" /></p>
          </form>
        </div>
        <div class="member-menu-top">
          <p class="member-check"><input type="checkbox" /></p>
          <p class="bt-member"><a class="bt-remove"><span>remove</span></a><a class="bt-block"><span>block</span></a></p>
        </div>
        <div class="filter-member">
          <ul>
            <li class="title">order by :</li>
            <li class="active"><a>Star point</a></li>
            <li><a>Happy point</a></li>
            <li><a>Friends</a></li>
            <li><a>Name</a></li>
            <li><a>Recent Activity</a></li>
          </ul>
        </div>
        <div class="details">
          <table cellpadding="0" cellspacing="0">
            <tr class="hidden-template">
			  <td class="member-check"><input type="checkbox" /></td>
              <td class="app-list">
                <div class="detail-list-style02">
                  <p class="thumb"><a><img src="<?php echo base_url()."assets/images/default/user.png";?>"></a></p>
                  <h2></h2>
                   <p><a class="view-fb">View facebook profile</a></p>
                  <!--<ul class="member-label">
                    <li><a class="label-1"><span></span></a></li>
                    <li><a class="label-2"><span></span></a></li>
                  </ul>-->
				</div>
              </td>
             <td class="status">Last active<b></b></td>
              <td class="status">Join since<b></b></td>
              <td class="status1"><img src="<?php echo base_url()."assets/images/bg/bg_member-star.png";?>" /><span> Star point</span><b></b></td>
              <td class="status1"><img src="<?php echo base_url()."assets/images/bg/bg_member-star.png";?>" /><span> Happy point</span><b></b></td>
              <td class="status1">Friends<b></b></td>
              <td class="bt-icon"><a class="bt-go"><span>go</span></a></td>
            </tr>
          </table>
        </div>
		<div class="member-menu-btm">
          <p class="member-check"><input type="checkbox" /></p>
          <p class="bt-member"><a class="bt-remove"><span>remove</span></a><a class="bt-block"><span>block</span></a></p>
          <div class="paging pagination-users"></div>
        </div>
		
		<?php } else { ?>
		<div class="blank-tab white-box-01" style="background-image: url('<?php echo base_url(); ?>assets/images/bg/blank_member.jpg');">
			<h2>There's no member yet.</h2>
			<p class="sub-title">Do you want to add one?</p>
			<hr />
			<h3>Why do I have to add member?</h3>
			<ul>
				<li>Tell users why they have to add member.</li>
				<li>At vero eos et accusamus et iusto odio dignissimos ducimus cord upti ducid qui blanditiis atque .</li>
				<li>Praesentium voluptatum deleniti atque corrupti ducid muntibo quos dolores et quas molestias excepturi sint occaecat</li>
				<li>At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis </li>
				<li>Praesentium voluptatum deleniti atque corrupti ducid muntibo quos dolores et quas molestias excepturi sint occaecat</li>
			</ul>
			<a class="bt-learn-more-campaign" href="<?php echo base_url().''; ?>">Learn more about campaign</a>
			<br />
		</div>
		<?php } ?>
		<br />
      </div>
