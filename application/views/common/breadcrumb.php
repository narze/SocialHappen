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
    <ul class="tool">
      <li class="group"><a href="#"><span>group</span></a></li>
      <li class="setting"><a href="<?php echo issetor($settings_url,'#');?>"><span>setting</span></a></li>
    </ul>
</div>
