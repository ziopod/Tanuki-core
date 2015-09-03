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
* Welcome route (default route)
**/
Route::set('welcome', '(<controller>(/<action>(/<slug>)))')
	->defaults(array(
		'controller'	=> 'Pages',
		'action'		=> 'read',
		'slug'			=> 'welcome',
	));

/**
* Autoload pages
**/
Route::set('pages', '<slug>', array(
		// 'slug'	=> 'my_page', // restrict a specific url
		// 'slug'	=> '.*', // for any extension in url
		// 'slug'	=> '[a-zA-Z0-9_/]+', // for subfolder
	))
	->defaults(array(
		'controller'	=> 'Pages',
		'action'		=> 'read',
	));