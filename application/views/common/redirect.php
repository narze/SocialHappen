<script type="text/javascript">
	window.onload = function(){
		<?php if(isset($refresh_parent) && $refresh_parent) : ?>
			window.parent.location.reload();
		<?php elseif(isset($refresh) && $refresh) : ?>
			window.location.reload();
		<?php elseif(isset($redirect_parent)) : ?>
			window.parent.location.replace($redirect_parent);
		<?php elseif(isset($redirect)) : ?>
			window.location.replace($redirect);
		<?php endif; ?>	
	};
</script>