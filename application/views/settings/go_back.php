<?php if(isset($company)):?>
	<div class="title-name">
		<p class="nav"><?php echo anchor(base_url()."company/{$company['company_id']}","&laquo; Back to {$company['company_name']} company"); ?></p>
	</div>
<?php endif; ?>
