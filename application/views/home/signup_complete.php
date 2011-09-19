<div class="popup_company-thanks">
    <h2 class="in-popup"><?php echo $package['package_name']; ?> Selected</h2>
    <div id="package-image">
		<img src="<?php echo $package['package_image'] ? $package['package_image'] : base_url().'images/package_icon_default.png' ; ?>" />
	</div>
	<div id="package-detail">
		<p class="thanks">Thank you!</p>
		<p class="message">You are using <?php echo $package['package_name']; ?>.</p>
		<p><a class="bt-continue" style="position: relative;top: 10px;left: -180px;">Continue</a></p>
	</div>
</div>