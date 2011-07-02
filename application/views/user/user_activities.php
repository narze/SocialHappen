<div class="wrapper-details activities">
	<h2 class="application"><span>Activities</span></h2>
	<?php echo '<ul>';
		foreach ($activity as $item) {
			echo '<li>' . $item . '</li>' . "\n";
		}
		echo '</ul>';
	 ?>
</div>
