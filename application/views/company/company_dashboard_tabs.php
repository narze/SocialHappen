<div>
	<div class="tab-content">
		<ul>
			<li class="active page_tab">
				<a href="#" onclick="shDragging.select_page_tab(); return false;">
				<span class="page-installed-count">Page (0)</span>
				</a>
			</li><!-- 
			<li class="app_tab">
				<a href="#" onclick="select_app_tab(); return false;">
				<span class="app-installed-count">Application (0)</span>
				</a>
			</li> -->
		</ul>
	</div>
	<div class="left-panel">
	</div>
	<div class="app-tab-left" style="display:none;">
		<p class="head-dragging-app">
			<strong></strong>
			<b></b>
		</p>
		<div class="dragging-app">
			<div>
				<ul>
				</ul>
			</div>
		</div>
		<div class="strip">
			<ul>
			</ul>
		</div>
	</div>
	<div class="page-tab-left" style="display:none;">
		<?php if($user_have_package) { ?>
		<div class="box-page_list">
			<div class="dragging-page">
				<a class="back-inactive" href="javascript:shDragging.previous_page('installed-page')">
				<span>back</span>
				</a>
				<a class="next" href="javascript:shDragging.next_page('installed-page')">
				<span>next</span>
				</a>
				<div>
					<ul>
						<li class="add-page"></li>
					</ul>
				</div>
			</div>
			<div class="box-app-list">
				<div class="notice" style="display:none">
				</div>
				<p class="head-box-app-list">
					<strong></strong>
					<b></b>
				</p>
				<div class="dragging-app">
					<div>
						<ul>
						</ul>
					</div>
				</div>
				<div class="strip">
					<ul>
					</ul>
				</div>
			</div>
		</div>
		<?php } ?>
	</div>
</div>