<div class="main-wrapper">
	<?php if($return_url) { ?><a class="back-to-app" target="_top">Back to app</a><?php } ?>
</div>

<div id="notifications">
	
	<div class="pagination-bar-top">
		<div class="paging">
			<ul>
				<li><a class="active">1</a></li>
				<li><a>2</a></li>
			</ul>
		</div>
	</div>
	
	<div class="notifications-list">
		<ul><?php 
			
			//Test data
			$notifications = array(
				array(
					'status'=>'unread',
					'title'=>'SocialHappen', 
					'desc'=>'Quisque iaculis, libero a pharetra euismod, orci sem aliquam turpis, nec rutrum est diam ac odio.',
					'date'=>'15 June 2011'
				),
				array(
					'status'=>'unread',
					'title'=>'FeedVideo', 
					'desc'=>'Quisque iaculis, libero a pharetra euismod, nec rutrum est diam ac odio.',
					'date'=>'14 June 2011'
				),
				array(
					'status'=>'read',
					'title'=>'FeedVideo', 
					'desc'=>'Quisque iaculis, libero a pharetra euismod, nec rutrum est diam ac odio.',
					'date'=>'14 June 2011'
				)
			); 
			//echo '<pre>'; print_r($notifications); echo '</pre>';
			
			if($notifications)
			{
				foreach($notifications as $notification) { ?>
				<li <?php echo $notification['status']=='unread' ? 'class="unread"' : ''; ?>>
					<div class="wrapper">
						<img src="" />
						<p class="title"><?php echo $notification['title'] ?></p>
						<div class="desc"><?php echo $notification['desc'] ?><div class="date"><?php echo $notification['date'] ?></div>
							<div class="action">
								<a class="bt-remove">Delete</a>
							</div>
						</div>
					</div>
				</li><?php
				}
			} else { ?>
				<li>No notification</li><?php
			} ?>
		</ul>
	</div>
	
	<div class="pagination-bar-bottom">
		<div class="paging">
			<ul>
				<li><a class="active">1</a></li>
				<li><a>2</a></li>
			</ul>
		</div>
	</div>
	
</div>