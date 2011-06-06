<div id="page-apps">
	<div id="page-apps-header">
	App
	</div>
	<div id="page-apps-add-new-app-button">
	+ Add new app
	</div>
	<?php if(is_array(issetor($page_apps))) foreach($page_apps as $page_app) : ?>
	<div class="page-apps-row">
		<div class="page-app-image">
		</div>
		<div class="page-app-name">
		</div>
		<div class="page-app-detail">
		</div>
		<div class="page-app-install-status">
		</div>
		<div class="page-app-user">
		</div>
		<div class="page-app-active-user">
		</div>
		<div class="page-app-actions">
			<div class="page-app-goto-page">
			Go to page
			</div>
			<div class="page-app-setting">
			Setting
			</div>
			<div class="page-app-remove">
			Remove
			</div>
			<div class="page-app-more">
			>
			</div>
		</div>
	</div>
	<?php endforeach; ?>
</div>