<?php
class App_facebook_register_model extends CI_Model {
	var $app_install_id = '';
	var	$email	=	'';	//req
	var	$prefix	=	'';
	var	$first_name	=	''; //req
	var	$middle_name	=	'';
	var	$last_name	=	'';	//req
	var	$job_title	=	'';
	var	$organization	=	'';
	var	$country	=	'';
	var	$address1	=	'';
	var	$address2	=	'';
	var	$city	=	'';
	var	$state	=	'';
	var	$zip	=	'';
	var	$mobile_phone	=	'';	//req
	var	$work_phone	=	'';
	var	$extension	=	'';
	var	$fax	=	'';
	//extend
	var	$date_of_birth	=	'';
	var	$gender	=	'';
	var	$emerg_contact_name	=	'';
	var	$emerg_contact_phone	=	'';
	var	$upload_photo	=	'';
	var	$id_card_no	=	'';
	var	$membership_no	=	'';
	var	$facebook_id	=	'';
	var $note	=	'';
	
	function __construct()
	{
		parent::__construct();
	}
}
?>