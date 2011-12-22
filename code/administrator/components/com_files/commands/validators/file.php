<?php
/**
 * @version     $Id$
 * @category	Nooku
 * @package     Nooku_Server
 * @subpackage  Files
 * @copyright   Copyright (C) 2011 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * File Validator Command Class
 *
 * @author      Ercan Ozkaya <http://nooku.assembla.com/profile/ercanozkaya>
 * @category	Nooku
 * @package     Nooku_Server
 * @subpackage  Files
 */

class ComFilesCommandValidatorFile extends KCommand
{
	protected function _databaseBeforeSave($context)
	{
		$row = $context->caller;

		if (is_string($row->file))
		{
			// remote file
			try {
				$file = $this->getService('com://admin/files.database.row.url');
				$file->setData(array('file' => $row->file));
				$file->load();
				$row->contents = $file->contents;
				
			} catch (ComFilesDatabaseRowUrlException $e) {
				throw new KControllerException($e->getMessage(), $e->getCode());
			}

			if (empty($row->name))
			{
				$uri = $this->getService('koowa:http.url', array('url' => $row->file));
	        	$path = $uri->get(KHttpUrl::PATH | KHttpUrl::FORMAT);
	        	if (strpos($path, '/') !== false) {
	        		$path = basename($path);
	        	}

	        	$row->name = $path;
			}
		}
		
		return $this->getService('koowa:filter.factory')->instantiate('com://admin/files.filter.file.uploadable')->validate($context);
	}
}