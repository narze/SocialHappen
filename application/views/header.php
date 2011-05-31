<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>

<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta name="description" content="SocialHappen" />

<title>SocialHappen - <?php if (isset($title)) {
                        echo $title;
                }
                else
                {
                echo 'Untitled Page';        
                }
                ?>
</title>
</head>
<body>
	<div id="container">
		<header>
			<?php $this->load->view('bar_view'); ?>
		</header>