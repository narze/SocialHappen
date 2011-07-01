<?php if(isset($company)):?>
	<div class="title-name">
		<p class="thumb"><img src="<?php echo imgsize($company['company_image'],25); ?>" /></p>
		<h2><?php echo $company['company_name']; ?></h2>
	</div>
<?php endif; ?>