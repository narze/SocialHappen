<div class="main-wrapper">
	<?php if($return_url) { ?>
	<a class="go-back back-to-app" target="_top">Back to app</a>
	<?php } else { ?>
	<a class="go-back a-dashboard">Back to Dashboard</a>
	<?php } ?>
</div>

<div id="notifications">
	
	<div class="pagination-bar-top paging"></div>
	
	<div class="notifications-list">
		<ul>
			<li>
				<div class="wrapper">
					<img src="" />
					<p class="title"></p>
					<div class="desc">
						<p></p>
						<div class="date"></div>
						<div class="action"><a class="bt-remove">Delete</a></div>
					</div>
				</div>
			</li>
		</ul>
	</div>
	
	<div class="pagination-bar-bottom paging"></div>
	
</div>