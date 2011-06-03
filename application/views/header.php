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
<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js"></script>
</title>
</head>
<body>
	<div id="container">
		<div id="header">
			<?php $this->load->view('bar_view'); ?>
		</div>