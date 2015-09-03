<?php defined('SYSPATH') OR die('No direct script access.');

/**
* Sync content
*
* @package		Tanuki
* @category		Controller
* @author		Ziopod <ziopod@gmail.com>
* @copyright	(c) 2013-2014 Ziopod
* @license		http://opensource.org/licenses/MIT
**/

class Controller_Sync extends Controller {

	/**
	* Syn with Git depot
	**/
	public function action_git()
	{

		// Current directory
		$original_dir = getcwd();

		// Go to content directory
		chdir(DOCROOT . Flatfile::CONTENTDIR);
		$remote = Kohana::$config->load('tanuki.git.remote');
		$branch = Kohana::$config->load('tanuki.git.branch');
		// Pull from bare depot
		exec("git pull $remote $branch");

		// Back to original dir
		chdir($original_dir);

	}
}
