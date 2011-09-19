<div class="payment-summary">
    <h2>Payment Summary</h2>
    <p><b>Billing Information</b> <?php echo $order['billing_info'];?></p>
    <p><b>Package</b> <?php echo $order['package']; ?> (<?php echo $order['order_net_price']? '<span class="price">'.$order['order_net_price'].' USD</span>' : 'FREE' ; ?>)</p>
    <p><b>Payment Method</b> <?php echo $order['payment_method']; ?></p>
	<a class="bt-continue" href="<?php echo base_url().'?logged_in=true'; ?>"><span>Continue</span></a>

</div>