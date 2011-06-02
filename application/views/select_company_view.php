<?php $this->load->view('header'); ?>
<h1>Select company</h1>
	<h3>Or <?php echo anchor('home/create_company','create new company'); ?></h3>
	<ul>
		<?php foreach($user_companies as $company) : ?>
			<li><pre><?php var_dump($company); ?></pre></li>
		<?php endforeach; ?>
	</ul>
<?php $this->load->view('footer');