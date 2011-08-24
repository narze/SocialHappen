<div class="popup_payment-confirm">
    <h2>Payment Confirm</h2>
    <p><b>Billing Information</b> <?php echo $order['billing_info']; ?></p>
    <p><b>Package</b> <?php echo $order['package']; ?> (<?php echo $order['order_net_price']? '<span class="price">'.$order['order_net_price'].' THB</span>' : 'FREE' ; ?>)</p>
    <p><b>Payment Method</b> <?php echo $order['payment_method']; ?></p>
	<a href="#">cancel</a> <a class="bt-continue"><span>Continue</span></a>
</div>