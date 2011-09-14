<div class="popup_payment-complete">
    <h2 class="in-popup">Payment Complete</h2>
    <div id="package-image">
		<img src="<?php echo $package['package_image'] ? $package['package_image'] : base_url().'images/package_icon_default.png' ; ?>" />
	</div>
	<div id="package-detail">
		<p class="thanks">Thank you!</p>
		<p class="message">You've upgraded to <?php echo $package['package_name']; ?>.</p>
		<p><a class="bt-continue" style="position: relative;top: 10px;left: -180px;">Back to dashboard</a></p>
	</div>
</div>