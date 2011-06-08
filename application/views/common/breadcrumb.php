<div id="breadcrumb">
	<?php
		$links = '';
		foreach($breadcrumb as $each){
			$links .= "<a href='{$each['url']}'>{$each['name']}</a> >";
		}
		echo rtrim($links, '>');
	?>
</div>