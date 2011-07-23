<div class="tab-menumain">
    <ul class="menu">
      <li class="home"><a href="<?php echo base_url();?>"><span>home</span></a></li>
		<?php
		$last = end($breadcrumb);
		foreach($breadcrumb as $name => $url){
			if($last == $url){
				echo "<li>{$name}</li>";
			} else {
				echo "<li><a href='{$url}'>{$name}</a></li>";
			}
		}
		?>
    </ul>
	<?php if(isset($settings_url)): ?>
		<ul class="tool">
		  <li class="setting"><a href="<?php echo $settings_url;?>"><span>setting</span></a></li>
		</ul>
	<?php endif; ?>
</div>
