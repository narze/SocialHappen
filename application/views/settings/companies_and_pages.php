<div>
	<div class="border-none">
		<h2><span>Company Manage</span></h2>
        <div class="box-company-manage">
			<h2>Details<span>Tools</span></h2>
			<?php foreach($user_companies as $company) : ?>
				<div class="detail-list">
					<ul>
						<li class="user-company-setting">
							<span>
								<img alt="" src="<?php echo imgsize($company['company_image'],'square');?>" /><?php echo "Company {$company['company_name']}"; ?>
							</span>
							<span class="tools">
								<a class="bt-setting_company user-company-setting" href="<?php echo base_url()."settings/company/{$company['company_id']}";?>">
									<span>setting</span>
								</a>
								<a class="bt-delete_company" href="#">
									<span>delete</span>
								</a>
							</span>
							<ul>
								<?php foreach($company_pages[$company['company_id']] as $page) : ?>
									<li class="company-page-setting">
									&raquo <img alt="" src="<?php echo imgsize($page['page_image'],'square');?>" /><?php echo "Page {$page['page_name']}"; ?>
										<span class="tools">
											<a class="bt-setting_company company-page-setting" href="<?php echo base_url()."settings/page/{$page['page_id']}";?>">
												<span>setting</span>
											</a>
											<a class="bt-delete_company" href="#">
												<span>delete</span>
											</a>
										</span>
									</li>
								<?php endforeach; ?>
		                    </ul>
						</li>
					</ul>
				</div>
			<?php	
			endforeach;
			?>
		</div>
    </div>     
</div>