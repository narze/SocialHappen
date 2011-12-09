<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = "home/package";
$route['404_override'] = 'error/index/404';
$route['error/(:num)'] = 'error/index/$1';
$route['company/(:num)'] = "company/index/$1";
$route['page/(:num)'] = "page/index/$1";
$route['app/(:num)'] = "app/index/$1";
$route['campaign/(:num)'] = "campaign/index/$1";
$route['tab/(:num)'] = "tab/index/$1";
$route['facebook/(:num)'] = "tab/index/$1";
$route['share/(:num)'] = "share/index/$1";
$route['invite/(:num)'] = "invite/index/$1";
$route['settings/account/(:num)'] = "settings/account/index/$1";
$route['settings/campaign/(:num)'] = "settings/campaign/index/$1";
$route['settings/company_pages/(:num)'] = "settings/company_pages/index/$1";
$route['settings/company/(:num)'] = "settings/company/index/$1";
$route['settings/package/(:num)'] = "settings/package/index/$1";
$route['settings/page/(:num)'] = "settings/page/index/$1";
$route['settings/page/signup_form/(:num)'] = "settings/page_signup_form/index/$1";
$route['settings/page/signup_form/(:any)'] = "settings/page_signup_form/$1";
$route['settings/page/user_class/(:num)'] = "settings/page_user_class/index/$1";
$route['settings/page/user_class/(:any)'] = "settings/page_user_class/$1";
$route['settings/app_component/homepage/(:num)'] = "settings/app_component_homepage/index/$1";
$route['settings/app_component/homepage/(:any)'] = "settings/app_component_homepage/$1";
$route['settings/app_component/invite/(:num)/(:num)'] = "settings/app_component_invite/index/$1/$2";
$route['settings/app_component/invite/(:any)'] = "settings/app_component_invite/$1";
$route['settings/app_component/sharebutton/(:num)/(:num)'] = "settings/app_component_sharebutton/index/$1/$2";
$route['settings/app_component/sharebutton/(:any)'] = "settings/app_component_sharebutton/$1";
$route['settings/page_apps/(:num)'] = "settings/page_apps/index/$1";
$route['settings/page_signup_fields/(:num)'] = "settings/page_signup_fields/index/$1";
$route['settings/page_user_class/(:num)'] = "settings/page_user_class/index/$1";


/* End of file routes.php */
/* Location: ./application/config/routes.php */