<div class="wrapper-details apps">
	<h2 class="application"><span>Appication</span></h2>
	<?php if($app_count > 0) { ?>
	<div class="option apps"><a class="bt-addnew_app"><span>Add new appication</span></a></div>
	<div class="details apps">
		  <table cellpadding="0" cellspacing="0">
			<tr class="hidden-template">
			  <td class="app-list">
				<div class="detail-list-style01">
				  <p class="thumb"><img src="<?php echo base_url()."assets/images/default/app.png";?>"></p>
				  <h2></h2>
				  <p class="description"></p>
				</div>
			  </td>
			  <td class="status app-status">Status <span></span></td>
			  <td class="status app-member">Member <b></b></td>
			  <td class="status1 app-monthly-active">Monthly active user <b></b></td>
			  <td class="bt-icon"><a class="bt-edit" title="Edit"><span>Edit</span></a></td>
			  <td class="bt-icon"><a class="bt-setting" title="Setting"><span>Setting</span></a></td>
			  <td class="bt-icon"><a class="bt-delete" title="Delete"><span>Delete</span></a></td>
			  <td class="bt-icon"><a class="bt-go apps" title="Go"><span>Go</span></a></td>
			</tr>
		</table>
	    <div class="pagination-apps strip"></div>
	</div>
	<?php } else { ?>
	<div class="blank-tab white-box-01" style="background-image: url(<?php echo base_url(); ?>assets/images/bg/blank_app.jpg);">
		<h2>There's no application was installed</h2>
		<p class="sub-title">Do you want to add one?</p>
		<div class="new-item"><a class="bt-addnew_app"><span>Add new appication</span></a></div>
		<hr />
		<h3>Why do I have to install these application?</h3>
		<ul>
			<li>Tell users why thay have to install the application on the page</li>
			<li>At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis </li>
			<li>Praesentium voluptatum deleniti atque corrupti ducid muntibo quos dolores et quas molestias excepturi sint occaecat</li>
		</ul>
		<a class="bt-view-all-apps" href="<?php echo base_url().'home/apps'; ?>">View all applications' details</a>
	</div>
	<?php } ?>
	<br />
	<div id="hidden-notice" style="display:none">
		<div class="goto-facebook app-installed">
			<h2 class="in-popup">App installed</h2>
			<p><b>You can see your installed app in facebook</b></p>
			<a class="bt-go-facebook" target="_top">Go to facebook</a>
		</div>
	</div>
</div>