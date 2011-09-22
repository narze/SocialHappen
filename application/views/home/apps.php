<div class="wrapper-package">
	
	<div class="wrapper-details">
		<div class="packages-intro-text">
			<h2>Choose the application that's best for your needs</h2>
			<p>Sed ut perspiciatis unde omnis iste natus saer sit.</p>
		</div>
	
		<ul class="apps" style="background:white url(../../images/bg/bg_line-gray.gif) 265px 0 repeat-y;position:relative"><?php
		foreach($apps as $app) 
		{	?>
			<li id="app-<?php echo $app['app_id']; ?>" class="app" style="display:inline-block;width:100px;height:100px;position:relative;overflow:hidden;padding:20px;text-align:center;">
				<a href="apps/<?php echo $app['app_id']; ?>">
					<img src="<?php echo $app['app_image']; ?>" title="<?php echo $app['app_name']; ?>" style="width:64px;height:64px;" />
				</a>
				<p>
					<a href="apps/<?php echo $app['app_id']; ?>" style="display:block;width:100px;height:18px;overflow:hidden;font-weight:bold;"><?php echo $app['app_name']; ?></a>
					<?php echo $app['app_description']; ?>
				</p>
			</li><?php
		} ?>
		</ul>
		<br />
	</div>
</div>
<div class="remark">
	<p><b>Remark</b></p>
    <p><span class="asterisk">*</span> Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium.</p>
    <p><span class="asterisk">**</span> Totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt.</p>
	<div class="more-info">
		<b>For more information</b>
		<a class="bt-read-faqs">Read FAQs</a>
	</div>
</div>