<?php
defined('BASEPATH') OR exit('No direct script access allowed');

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
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'admin';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['ReplistAdmin'] = 'admin/index';
$route['login'] = 'admin/login';

$route['dashboard'] = 'admin/dashboard';
$route['admin-profile'] = 'admin/adminprofile';
$route['edit-admin-profile/(:any)'] = 'admin/editadminprofile/$1';
$route['update-admin-profile/(:any)'] = 'admin/updateadminprofile/$1';
$route['password'] = 'admin/password';
$route['change-password/(:any)'] = 'admin/changepassword/$1';

$route['reps'] = 'admin/reps';
$route['view-rep/(:any)'] = 'admin/viewrep/$1';

$route['buyer'] = 'admin/buyer';
$route['view-buyer/(:any)'] = 'admin/viewbuyer/$1';

$route['report'] = 'admin/report';
$route['sales-report'] = 'admin/salesreport';
$route['purchase-report'] = 'admin/purchasereport';

$route['order-history/(:any)'] = 'admin/orderhistory/$1';
$route['view-order/(:any)'] = 'admin/vieworder/$1';

$route['document'] = 'admin/document';
$route['view-document/(:any)'] = 'admin/viewdocument/$1';


$route['helpsupport'] = 'admin/helpsupport';
$route['reply/(:any)'] = 'admin/supportreply/$1';

$route['contact'] = 'admin/contact';
$route['contact-reply'] = 'admin/contactreply';

$route['logout'] = 'admin/logout';

$route['reward'] = 'admin/reward';

$route['category'] = 'admin/category';
$route['add-category'] = 'admin/addcategory';
$route['edit-category/(:any)'] = 'admin/editcategory/$1';

$route['banner'] = 'admin/banner';
$route['add-banner'] = 'admin/addbanner';
$route['edit-banner/(:any)'] = 'admin/editbanner/$1';

$route['faq'] = 'admin/faq';
$route['add-faq'] = 'admin/addfaq';


$route['privacy-security'] = 'admin/privacy';
$route['product'] = 'admin/product';


$route['purchase-report/(:any)'] = 'admin/purchasereport/$1';








