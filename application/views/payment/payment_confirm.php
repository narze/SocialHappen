<div class="popup_payment-confirm">
    <h2>Payment Complete</h2>
    <p><b>Billing Information</b> <?php echo $order['user_name']; ?></p>
    <p><b>Package</b> <?php echo $order['item_name']; ?></p>
    <p><b>Payment Method</b> <?php echo $order['payment_method']; ?> (<?php echo $order['order_net_price']? '<span class="price">'.$order['order_net_price'].' THB</span>' : 'FREE' ; ?>)</p>
</div>