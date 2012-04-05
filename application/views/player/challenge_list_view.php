
{header}
	<div class="container-fluid">

		<div class="page-header">
			<h1><?php echo $company['company_name']?></h1>
		</div>

		<?php 
		if($challenges) 
		{
			foreach($challenges as $challenge) : ?>
				<div class="row-fluid">
					<p class="span2">
						<img class="span2" src="<?php echo $challenge['detail']['image'] ? $challenge['detail']['image'] : base_url('assets/images/default/challenge.png'); ?>" alt="">
					</p>
					<div class="span10">
						<h3><?php echo $challenge['detail']['name']; ?></h3>
						<p><?php echo $challenge['detail']['description']; ?></p>
						<p><?php echo anchor('player/challenge/'.$challenge['hash'], 'View', 'class="btn btn-primary"').' '; ?></p>
						<p><?php echo anchor('r/c?hash='.$challenge['hash']); ?></p>
					</div>
				</div><?php 
			endforeach;
		} else { ?>
			<div class="alert alert-info">
				No challenge
			</div><?php
		} ?>
		

	</div>