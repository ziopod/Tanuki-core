<?php defined('SYSPATH') OR die ('No direct script access');
/**
* # Tanuki Controller
*
* This basic controller provide autorendering for Flatfile content based on requested route
*
* @package		Tanuki
* @category		Controller
* @author		Ziopod <ziopod@gmail.com>
* @copyright	(c) 2013-2014 Ziopod
* @license		http://opensource.org/licenses/MIT
**/

class Controller_Tanuki extends Controller {

	/**
	* @var Layout Default Mustache layout
	**/
	public $layout;

	/**
	* @var View Mustache view to render
	**/
	public $view;

	/**
	* @var Boolean Auto render layout
	**/
	public $auto_render = TRUE;

	/**
	* Load [Kostache_Layout] and POO View
	*
	* Attempts to load a POO View based on the names of the Controller and Action requested
	**/
	public function before()
	{
		parent::before();

		if ($this->auto_render === TRUE)
		{

			$this->layout = Kostache_Layout::factory('layout/default');
			$view = 'View_' . ucfirst(Request::initial()->controller()) . '_'  . ucfirst(Request::initial()->action());
			if (class_exists($view))
			{
				$this->view = new $view;	
			}
		}
	}

	/**
	* Assign the [Kostache_Layout] render as the request response
	**/
	public function after()
	{
		parent::after();
		
		if ($this->auto_render)
		{

			if (isset($this->view))
			{
				$this->response->body($this->layout->render($this->view));
			}
			else
			{
				$this->response->body('View Model <code>View_' . ucfirst(Request::initial()->controller()) . '_' . ucfirst(Request::initial()->action()) . '</code> not found!');
			}			
		}
	}
}