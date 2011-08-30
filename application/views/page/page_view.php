{header}
{company_image_and_name}
{breadcrumb}
<div class="wrapper-content">
	<div>
		{page_profile}
		{page_tabs}<?php 
		if($is_package_over_the_limit) { ?>
			{package_limited}<?php 
		} else { ?>
			{page_apps}
			{page_campaigns}
			{page_users}
			{page_report}<?php 
		} ?>
	</div>
	<div class="bottom"><!--bottom--></div>
</div>
{footer}