	<h2><span>Activity Stat</span></h2>
	<div id="user-stat"></div>
	
	<h2><span>Activities</span></h2>
	<div class="activities-table">
		<div class="activities-table-head"> </div>
		<table>
			<thead>
				<tr>
					<th>Page</th>
					<th>Application</th>
					<th>Campaign</th>
					<th>Activity Detail</th>
					<th>Date</th>
				</tr>
			</thead>
			<tbody><?php
				if($activities)
				{
					foreach ($activities as $activity) { ?>
						<tr>
							<td><?php echo issetor($activity['page_name'], '-'); ?></td>
							<td><?php echo issetor($activity['app_name'], '-'); ?></td>
							<td><?php echo issetor($activity['campaign_name'], '-'); ?></td>
							<td><?php echo issetor($activity['activity_detail'], '-'); ?></td>
							<td><?php echo date('d F Y', $activity['date']); ?></td>
						</tr><?php
					}
				}
				else { ?>
					<tr><td colspan="5" align="center">There's no recent activity.</td></tr><?php
				}
			 ?>
			</tbody>
		</table>
		<div class="activities-table-footer"> </div>
	</div>