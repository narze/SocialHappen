	<h2><span>Activity Stat</span></h2>
	<div id="user-stat"></div>
	
	<h2><span>Activities</span></h2>
	<div class="white-box-01">
		<ul class="activities">
		<?php
			if($activity)
			{
				foreach ($activity as $item) {
					echo '<li>' . $item . '</li>' . "\n";
				}
			}
			else { ?>
				<li>There's no recent activity.</li><?php
			}
		 ?>
		</ul>
	</div>