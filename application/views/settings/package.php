
  <div id="current-package">
	<h2><span>Current Package</span></h2>
	<div class="box-current_package">
	  <div class="detail">
		<h2><?php echo $current_package['package_name']; ?></h2>
		<p><?php echo $current_package['package_detail']; ?></p>
		<p class="thumb"><img src="<?php echo $current_package['package_image'] ? $current_package['package_image'] : base_url().'images/enterprise-deluxe.png' ; ?>" alt="<?php echo $current_package['package_name']; ?>" /></p>
	  </div>
	  <ul class="detail">
		<li><b><?php echo $user_companies.'/'.$current_package['package_max_companies']; ?></b><span>Companies</span></li>
		<li><b><?php echo $user_pages.'/'.$current_package['package_max_pages']; ?></b><span>Pages</span></li>
		<li><b><abbr title="<?php echo $members.'/'.$current_package['package_max_users']; ?>"><?php echo $members; ?></abbr></b><span>Members</span></li>
		<li class="package-apps"><img src="images/enterprise-package.png" alt="" />
			<?php if($apps) { ?>
			<div class="package-overlay" style="display:none;">
				<ul>
				  <?php foreach($apps as $app) { ?>
				  <li>
					<p><img style="width:64px;height:64px;" src="<?php echo $app['app_image']; ?>" alt="<?php echo $app['app_name']; ?>" /></p>
					<p><?php echo $app['app_name']; ?></p>
				  </li>
				  <? } ?>
				</ul>
			</div>
			<?php } ?>
		</li>
	  </ul>
	  <?php if($is_upgradable) { ?>
		<p><a class="bt-upgrade_package" href="<?php echo base_url().'home/package'; ?>"><span>Upgrade</span></a></p>
	  <?php } ?>
	  
	</div>
  </div>
  
  <div id="billing-information" class="border-none">
	<h2><span>Billing information</span></h2>
	<div class="box-billing_info">
	  <table cellpadding="0" cellspacing="0">
		<tr>
		  <th>Date</th>
		  <th>Invoice ID</th>
		  <th>Package name</th>
		  <th></th>
		</tr>
		<?php if($orders) 
		{
			foreach($orders as $order) { ?>
			<tr>
			  <td><?php echo $order['order_date']; ?></td>
			  <td><a href="#"><?php echo $order['order_id']; ?></a></td>
			  <td><?php echo $order['package_name']; ?></td>
			  <td class="right"><?php echo $order['order_net_price']; ?> USD</td>
			</tr>
			<?php } 
		} else { ?>
			<tr>
			  <td colspan="4" align="center">No invoice found.</td>
			</tr>
		<?php } ?>
	  </table>
	</div>
  </div>