<div class="tab-menumain">

    <ul class="menu">

      <li class="home"><a href="<?php echo base_url();?>"><span>home</span></a></li>

		<?php
		
		foreach($breadcrumb as $each){
			echo "<li><a href='{$each['url']}'>{$each['name']}</a></li>";
		}
		
		?>
    </ul>

    <ul class="tool">

      <li class="group"><a href="#"><span>group</span></a></li>

      <li class="setting"><a href="#"><span>setting</span></a></li>

    </ul>

  </div>
