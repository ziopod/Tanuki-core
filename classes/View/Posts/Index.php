<?php
/**
* # Showing page
*
* You can write your own stuffs here 
*
* @package		Tanuki
* @category		View Model
* @author		Ziopod <ziopod@gmail.com>
* @copyright	(c) 2013-2014 Ziopod
* @license		http://opensource.org/licenses/MIT
**/

class View_Posts_Index extends View_Master {

	/**
	* Return posts with pagination
	* 
	* @return array	list of post
	**/
	public function posts()
	{
		return Flatfile::factory('Post')->limit(5)->find_all();
	}
}