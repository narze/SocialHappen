<div id="page-profile">
	<div id="page-image">
		<img src="<?php echo issetor($page_profile['page_image']); ?>" />
	</div>
	<div id="goto-page-button">
		<a href="#">--Go to page--</a>
	</div>
	<div id="add-app">
		<a href="#">--Add app--</a>
	</div>
	<div id="page-name">
		<?php echo issetor($page_profile['page_name']); ?>
	</div>
	<div id="page-detail">
		<?php echo issetor($page_profile['page_detail']); ?>
	</div>
	<div id="page-info">
		---Information---
		<div id="page-new-user">
			--new member--
		</div>
		<div id="page-all-user">
			--all member--
		</div>
		<div id="page-likes">
			--likes--
		</div>
		<div id="page-apps-amount">
			--apps--
		</div>
		<div id="page-campaigns-amount">
			--campaigns--
		</div>
	</div>
</div>