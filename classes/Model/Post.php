<?php defined('SYSPATH') OR die ('No direct script access');

/**
* # Page Model
*
* Model for Tanuki page
*
* @package		Tanuki
* @category		Model
* @author		Ziopod <ziopod@gmail.com>
* @copyright	(c) 2013-2014 Ziopod
* @license		http://opensource.org/licenses/MIT
**/

class Model_Post extends Flatfile {

	/**
	* Apply filter on data
	**/
	public function filters()
	{
		return array(
			'excerpt' => array(
				array('Flatfile::Markdown'),
			),
			'content' => array(
				array('Flatfile::Markdown'),
			),
			'credit' => array(
				array('json_decode'),
			),
			'license' => array(
				array('json_decode'),
			),
			'tags' => array(
				array('str_to_list'),
			),
		);
	}
	
	/**
	* Return specifics data
	*
	* @return string
	**/
	public function url()
	{
		return URL::base(TRUE, FALSE) . 'read/' . $this->slug;
	}

	/**
	* Return a minimal string for post title
	*
	* @return string
	**/
	public function title()
	{
		if ( ! $this->title)
		{
			return ucfirst($this->slug);
		}

		return $this->title;
	}
}