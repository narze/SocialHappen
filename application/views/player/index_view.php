{header}
	<div class="container-fluid">

		<div class="row-fluid">
			<div class="span12">
					<p>
					<?php
						echo anchor('player/challenging_list', 'View Challenging Challenges', 'class="btn btn-primary"').' ';
						echo anchor('player/settings', 'Player settings', 'class="btn"').' ';
					 ?>
					<?php if($facebook_connected) : ?>
						You are connected to facebook
					<?php else : ?>
						You are not connected to facebook
					<?php endif; ?>
					</p>

					<?php 
						//echo '<p>Player status :</p> <pre>';
						//var_dump($user);
						//echo '</pre>';
					?>
			</div>
		</div>

		<div class="page-header">
			<h1>Shops</h1>
		</div>

		<?php 
		if($companies) 
		{
			foreach($companies as $company) : ?>
				<div class="row-fluid">
					<img class="span2" src="<?php echo $company['company_image']; ?>" alt="">
					<div class="span10">
						<h5><?php echo $company['company_name']; ?></h5>
						<p><?php echo $company['company_detail']; ?></p>
						<p><?php echo anchor('player/challenge_list/'.$company['company_id'], 'View Challenges', 'class="btn btn-primary"').' '; ?></p>
					</div>
				</div><?php 
			endforeach; 
		} else { ?>
			<div class="alert alert-info">
				No company
			</div><?php
		} ?>
		

	</div>

