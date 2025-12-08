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
// $route['ja']['GET'] = 'Auth/LoginController/a';

$route['default_controller'] = 'MainController';
// $route['default_controller'] = 'Welcome';

$route['home'] = 'user/home';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['login']['GET'] = 'Auth/LoginController/index';
$route['login']['POST'] = 'Auth/LoginController/login';

$route['logout']['GET'] = 'Auth/LogoutController/logout';

$route['profile']['GET'] = 'User/UserController/index';

$route['proximamente'] = 'ProximamenteController/index';

$route['googleauth'] = 'Auth/GoogleController/index';
$route['googleauth/logout'] = 'Auth/GoogleController/logout';

$route['wol'] = 'WolController/wol';
$route['dashboard'] = 'DashboardController/index';

// Kanban
$route['kanban'] = 'KanbanController/index';
$route['kanban/load_board/(:num)'] = 'KanbanController/load_board/$1';
$route['kanban/create_board'] = 'KanbanController/create_board';
$route['kanban/create_column'] = 'KanbanController/create_column';
$route['kanban/update_column_order'] = 'KanbanController/update_column_order';
$route['kanban/create_task'] = 'KanbanController/create_task';
$route['kanban/update_task_position'] = 'KanbanController/update_task_position';
$route['kanban/delete_board'] = 'KanbanController/delete_board';
$route['kanban/rename_board'] = 'KanbanController/rename_board';
$route['kanban/rename_column'] = 'KanbanController/rename_column';
$route['kanban/delete_column'] = 'KanbanController/delete_column';
$route['kanban/delete_task'] = 'KanbanController/delete_task';
$route['kanban/get_task_details'] = 'KanbanController/get_task_details';
$route['kanban/update_task'] = 'KanbanController/update_task';
$route['kanban/get_all_tags'] = 'KanbanController/get_all_tags';
$route['kanban/assign_tags_to_task'] = 'KanbanController/assign_tags_to_task';
// $route['kanban/update_tag_color'] = 'KanbanController/update_tag_color';
// $route['kanban/change_task_priority'] = 'KanbanController/change_task_priority';
$route['kanban/get_column_tasks_html'] = 'KanbanController/get_column_tasks_html';

//Mailjet
$route['mailjet'] = 'MailController/index';


//Mapa Dea
$route['mapa'] = 'MapaController/index';
$route['mapa/(:any)'] = 'MapaController/$1';