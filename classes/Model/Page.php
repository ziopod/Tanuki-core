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

class Model_Page extends Flatfile {

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
			'sections'	=> array(
				array(array($this, 'load_parts'), array(':value', 'sections')),
			),
		);
	}
	

	/**
	* Return specifics data
	**/
	public function url()
	{
		return URL::base(TRUE, TRUE) . $this->slug;
	}

	/**
	* Load adittionnal content part
	*
	* @param	string	$parts			part name or list of part names
	* @param	string	$sub_directory	subdirectory name
	*
	* @return	array
	**/

	public function load_parts($parts, $subcategory = NULL)
	{
		$parts = func_get_arg(0);
		$sub_directory = func_get_arg(1);
		$sub_directory = $sub_directory ? $sub_directory . '/' : NULL;
		$result = array();

		if ( strpos($parts, ',') === FALSE)
		{
			// Just one entry
			try
			{
				$result[] = new Model_Page($sub_directory . $parts);
			}
			catch(Kohana_exception $e)
			{
				Log::instance()->add(Log::WARNING, $e->getMessage());
			}
			
		}
		else
		{
			// Multiples entries
			foreach (explode(',', $parts) as $part)
			{
				try
				{
					$result[] = new Model_Page($sub_directory . $part);
				}
				catch(Kohana_exception $e)
				{
					Log::instance()->add(Log::WARNING, $e->getMessage());
				}

			}
		}

		return $result;
	}

}