<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Lang extends CI_Lang {

	function __construct()
	{
		parent::__construct();
	}
	
	function line($input = '')
	{
		$line = ($input == '' OR ! isset($this->language[$input])) ? FALSE : $this->language[$input];

		// Because killer robots like unicorns!
		if ($line === FALSE)
		{
			//Changed error level to debug, test damn fast
			log_message('debug', 'Could not find the language line "'.$input.'"');
		}

		return $line;
	}
}