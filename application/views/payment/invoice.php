<?php if($print_view) { ?>
<link rel="stylesheet" type="text/css"  href="<?php echo base_url(); ?>assets/css/common/main.css" />
<?php } ?>
<div class="billing-detail<?php echo $print_view ? ' print' : ''; ?>">
    <?php if(!$print_view) { ?>
	<h2 class="in-popup">Billing Detail</h2>
	<?php } else { ?>
	<div class="print-bar"><button onclick="window.print(); return false;" >Print</button></div>
	<?php } ?>
    <table class="billing<?php echo $print_view ? ' print' : ''; ?>">
		<thead>
			<tr>
				<th>Client</th>
				<th width="146">Provider</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					<b>Customer name:</b> <?php echo $user['user_first_name'].' '.$user['user_last_name']; ?><br />
					<b>Email:</b> <?php echo $user['user_email']; ?>
				</td>
				<td>SocilaHappen.com</td>
			</tr>
		</tbody>
	</table>
	
	<table class="billing<?php echo $print_view ? ' print' : ''; ?>">
		<thead>
			<tr>
				<th>Order Number</th>
				<th>Invoice Number</th>
				<th>Invoice Date</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>OD<?php echo $order['order_id']; ?></td>
				<td>IN<?php echo $order['order_id']; ?></td>
				<td><?php echo date('Y/m/d', strtotime($order['order_date'])); ?></td>
			</tr>
		</tbody>
	</table>
	
	<table class="billing<?php echo $print_view ? ' print' : ''; ?>">
		<thead>
			<tr>
				<th>Package Name</th>
				<th style="text-align:right">Price</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><?php echo $order['package_name']; ?></td>
				<td align="right"><?php echo number_format($order['order_net_price']); ?> USD</td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td align="right">VAT :</td>
				<td align="right">0 USD</td>
			</tr>
			<tr>
				<td align="right">Total :</td>
				<td align="right"><b><?php echo number_format($order['order_net_price']); ?></b> USD</td>
			</tr>
		</tfoot>
	</table>
	<?php if(!$print_view) { ?>
	<p><a class="bt-print-invoice" target="_blank" href="<?php echo base_url().'payment/invoice/'.$order['order_id'].'?action=print'; ?>" >Print Invoice</a></p>
	<?php } ?>
</div>