<?php
/**
* # Tanuki view model
*
* Provide basic methods and properties for templating
*
* @package		Tanuki
* @category		View Model
* @author		Ziopod <ziopod@gmail.com>
* @copyright	(c) 2013-2014 Ziopod
* @license		http://opensource.org/licenses/MIT
**/

class View_Tanuki {

	/**
	* @var Autoload content
	**/
	public $autoload = TRUE;

	/**
	* @var Provide custom css class selector if needed (cf. layout/default.mustache)
	**/
	public $custom_css;

	/**
	* @var Store model name
	**/
	public $model_name;

	/**
	* Try to load Flatfile Model, based on url segment
	* Throw 404 error page if markdown file cant found
	**/

	public function __construct()
	{

		if ($this->autoload)
		{
			try
			{
				$model_name = Inflector::singular(strtolower(Request::current()->controller()));
				$model = 'Model_' . ucfirst($model_name);
				// Store model name
				$this->model_name = $model_name;

				/**
				* Assign model to a variable named as the controller name
				**/
				$this->$model_name = new $model(Request::initial()->param('slug'));
			}
			catch (Kohana_Exception $e)
			{
				if ($slug = Request::initial()->param('slug'))
				{
					throw HTTP_Exception::factory(404, __("Unable to find page :slug"), array(':slug' => $slug));		
				}
				
				throw HTTP_Exception::factory(404, __("Unable to find URI :uri"), array(':uri' => Request::initial()->uri()	));		
			}		
		}
	}

	/**
	* Stylesheet list
	*
	* Add your style like that:
	*
	*	return array(
	*		array(
	*			'src'	=> $this->base_url() . 'css/style.css',
	*			'media'	=> 'screen',
	*		),
	*	);
	*
	* @return  array
	**/
	public function styles()
	{
		return array();
	}

	/**
	* Scripts list
	*
	* Add your scripts like that:
	*
	*	return array(
	*		array(
	*		 	'src' => $this->base_url() . 'js/scripts.js',
	*		),
	*	);
	*
	* @return array
	**/
	public function scripts()
	{
		return array();
	}

	/**
	* Somes defaults globales data for all views
	*
	* Add your site informations like that: 
	*
	*	return array(
	*		'title' 		=> "Tanuki Get it simple!",
	*		'description'	=> "Just a simple web publishing design pattern",
	*		'author'		=> array(
	*			'name'		=> "Ziopod",
	*			'email'		=> "hello@ziopod.com",
	*			'url'		=> "http://ziopod.com",
	*		),
	*		'license'		=> array(
	*			'name'		=> 'MIT',
	*			'url'		=> 'http://opensource.org/licenses/mit-license.php',
	*		),
	*	);
	* 
	* @return	array	Global informations
	**/
	public function tanuki()
	{
		return array();
	}

	/**
	* Set HTML title tag
	*
	* @return	string
	**/
	public function title()
	{
		// Try to load title from model
		$model_name = $this->model_name;

		if (isset($this->$model_name->title))
		{
			return $this->$model_name->title;
		}

		// Instead use global config
		return Arr::path($this->tanuki(), 'title');
	}

	/**
	* Define main navigation
	*
	* Add your navigation like that:
	*
	*	return array(
	*		array(
	*			'url'		=> $this->base_url(),
	*			'name'		=> __('Home'),
	*			'title'		=> __('Go to Home'),
	*			'current'	=> Request::initial()->controller() === 'App' AND Request::initial()->action() === 'home',
	*		),
	*		array(
	*			'url'		=> $this->base_url() . 'about',
	*			'name'		=> __('Example page'),
	*			'title'		=> __('Go to example page'),
	*			'current'	=> Request::initial()->controller() === 'App' AND Request::initial()->param('slug') === 'about',
	*		),
	*	);
	*
	* @return 	array
	**/
	public function navigation()
	{
		return array();
	}

	/**
	* Test if navigation have some data
	* Usefull for contextual HTML tags like `<ul>`
	**/
	public function has_navigation()
	{
		return (bool) $this->navigation();
	}

	/**
	* Root URL
	*
	* @return	string
	**/
	public function base_url()
	{
		return URL::base(TRUE, TRUE);
	}

	/**
	* Current URL
	*
	* @return	string
	**/
	public function current_url()
	{
		return URL::site(Request::initial()->uri(), TRUE);
	}

	/**
	* Current year
	*
	* @return	string
	**/
	public function current_year()
	{
		return date('Y');
	}

	/**
	* Current lang
	**/
	public function lang()
	{
		return I18n::lang();
	}

}