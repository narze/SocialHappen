{header}
{breadcrumb}
<div class="wrapper-content">
	<div>
		<div class="wrapper-register">
			<?php if(isset($user) && $user) : ?>
			<div>
				You have already registered SocialHappen
			</div>
			<?php else : ?>
				<div>
				  <div>
					<h2>Tutorial</h2>
					<div><img src="images/banner-slider.png" alt="Tutorial" /></div>
				  </div>
					<div class="form">{signup_form}</div>
				</div>

			<?php endif; ?>
		</div>
	</div>
	<div class="bottom"><!--bottom--></div>
</div>
{footer}