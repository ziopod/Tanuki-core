<?php defined('SYSPATH') or die('No direct script access.');

/**
* Display posts
**/
Route::set('posts', 'posts(/<action>(/<slug>))')
	->defaults(array(
		'controller'	=> 'Posts',
		'action'		=> 'index',
	));

/**
* Short route for reading posts
**/
Route::set('read', 'read/<slug>')
	->defaults(array(
		'controller'	=> 'Posts',
		'action'		=> 'read',
	));

/**
* Display pages
* This rule loads pages in the same way as "posts" rule.
*
* But,
*
* We do not want display the term "pages" in the URL. We will use a catching
* rule that will load the flat file corresponding to the parts just after the
* domain name, eg http://domain.com/about chargeras le fichier
* "pages/about.md".
*
* As this rule will catch all parts after the domain name, it must not be
* declared in the module (otherwise it caught URLs before your own
* statements). Please write the following rule in your own bootstrap.php file
* just before your default route.
*
* Route::set('pages', '<slug>', array(
*		'slug'	=> 'my_page', // restrict to a specific url
*		'slug'	=> '.*', // for any extension in url
*		'slug'	=> '[a-zA-Z0-9_/]+', // for subfolder
*	))
*	->defaults(array(
*		'controller'	=> 'Pages',
*		'action'		=> 'read',
*	));
**/

Route::set('pages', 'pages(/<action>(/<slug>))')
	->defaults(array(
		'controller'	=> 'Page',
		'action'		=> 'index',
	));
/**
* Welcome home route (catch route when you have nothing after your domain)
**/
Route::set('welcome', '')
	->defaults(array(
		'controller'	=> 'Pages',
		'action'		=> 'read',
		'slug'			=> 'welcome',
	));