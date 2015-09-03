<?php defined('SYSPATH') OR die('No direct script access.');

/**
* Dropbox tests
*
* @package		Tanuki
* @category		Controller
* @author		Ziopod <ziopod@gmail.com>
* @copyright	(c) 2013-2014 Ziopod
* @license		http://opensource.org/licenses/MIT
**/

class Controller_Dropbox extends Controller{
	protected $_access_token;
	protected $_content_path;

	public function action_read()
	{
		// Utilisation du token dev pour le moment (restreint à une installation par Dropbox)
		Cookie::set('dpx_access_token', Kohana::$config->load('tanuki.dropbox.access_token'));

		if ( ! Cookie::get('dpx_access_token'))
		{
			$this->response->body('Veuillez vous connecté : <a href="' . URL::base(TRUE, TRUE) . 'dropbox/generatetoken">Connexion à Dropbox<a/>');		
		}
		else
		{
			// Store access token
			$this->_access_token = Cookie::get('dpx_access_token');
			$this->_content_path = DOCROOT . 'content';

			// Cette méthode pourras être activée via le webhook de Dropbox
			// https://www.dropbox.com/developers/webhooks/tutorial
			$this->action_sync();
		}

	}

	/**
	* Synchronisation du dossier distant Dropbox avec le dossier de contenu locale (via le webhook)
	**/
	/**
	* REFACTO : en premier lieu récupérer les données de manière récursive et ensuite faire la synchro locale
	*   - Ne pas récupérer les dossier non modifiée (check le hash)
	*   - Ne pas itérer sur les dossier supprimées, suprimé le dossier de manière récursive(limite les requetes vers Dropbox)
	*   - Synchronisation double sens pour l'usage multi-utilisateurs
	**/
	public function action_sync()
	{

		// Check if _dpx_changes exist
		if (! is_dir($this->_content_path . '/_dpx_changes'))
			mkdir ($this->_content_path . '/_dpx_changes');

		$this->_dpx_files_content($this->_dpx_files_metadata(''));
	}

	// TODO : Utiliser Delta
	// https://blogs.dropbox.com/developers/2013/12/efficiently-enumerating-dropbox-with-delta/
	protected function _dpx_files_metadata($path = '')
	{

		$request = Request::factory('https://api.dropboxapi.com/1/metadata/auto')
			->headers('Authorization', 'Bearer ' . $this->_access_token)
			->headers('Content-Type', 'application/json')
			->query('path', $path)
			->query('hash', TRUE)
			->query('include_deleted', TRUE)
			->query('include_media_info', TRUE)
			->execute();
		$request = json_decode($request);

		
		echo debug::vars("New request {$path} {$request->hash}");
		// echo debug::vars($request);

		return $request;
	}

	// TODO : exclude draft folders
	// TODO : control and limit size (150mo)
	protected function _dpx_files_content($response)
	{
		if ( ! $response)
			return FALSE;

		//Each file and folder
		foreach ($response->contents as $file)
		{
			// Update folder otherwise update file 
			if ($file->is_dir)
			{
				$dir_path = $this->_content_path . $file->path;

				if (isset($file->is_deleted))
				{
					echo debug::vars("Remove folder : $dir_path");
					$this->_rrmdir($dir_path);
				}
				else if ( ! is_dir($dir_path))
				{
					echo debug::vars("Create folder : $dir_path");
					mkdir($dir_path);
				}
				
				// echo debug::vars($file);

				// New iteration
				$this->_dpx_files_content($this->_dpx_files_metadata($file->path));
			}
			else
			{

				$file_content = Request::factory('https://content.dropboxapi.com/1/files/auto/')
					->headers('Authorization', 'Bearer ' . $this->_access_token)
					->headers('Content-Type', 'application/json')
					->query('path', $file->path)
					->execute()
					->body();

				$file_path = $this->_content_path . $file->path;
				
				// revision hash based on path + revision
				$dpx_hash_file = $this->_content_path . '/_dpx_changes/' . md5($file->path . $file->revision);

				if (file_exists($dpx_hash_file))
				{
					echo debug::vars("No change for : $file_path");
					continue;
				}
				
				echo debug::vars("Create / update file : $file_path");
				// echo debug::vars($file);
				
				touch($dpx_hash_file);

				if (isset($file->is_deleted))
				{
					if (is_file($file_path))
					{
						unlink($file_path);
					}
				}
				else
				{
					file_put_contents($file_path, $file_content, LOCK_EX);
				}

			}		
		}
	}

	/**
	* rmdir recursively helper
	* TODO: return list of deleted object
	**/
	protected function _rrmdir($dir)
	{
		if (is_dir($dir))
		{
			$objects = scandir($dir);

			foreach ($objects as $object)
			{
				if ($object != '.' AND $object != '..')
				{
					if (filetype($dir . DIRECTORY_SEPARATOR . $object) == 'dir')
					{
						$this->_rrmdir($dir . DIRECTORY_SEPARATOR . $object);
					}
					else
					{
						unlink($dir . DIRECTORY_SEPARATOR . $object);
					}
				}
			}

			rmdir($dir);

		}
	}
}