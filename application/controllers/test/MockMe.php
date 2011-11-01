<?php 
set_include_path(
    dirname(__FILE__) . '/../../libraries'
    . PATH_SEPARATOR . get_include_path()
);

require_once 'Mockery/Loader.php';

$loader = new \Mockery\Loader;
$loader->register();