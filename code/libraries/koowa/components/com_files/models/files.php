<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-files for the canonical source repository
 */

/**
 * Files Model
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
class ComFilesModelFiles extends ComFilesModelNodes
{
    public function getList()
    {
        if (!isset($this->_list))
        {
            $state = $this->_state;

            $files = $state->container->getAdapter('iterator')->getFiles(array(
        		'path'    => $this->_getPath(),
        		'exclude' => array('.svn', '.htaccess', '.git', 'CVS', 'index.html', '.DS_Store', 'Thumbs.db', 'Desktop.ini'),
        		'filter'  => array($this, 'iteratorFilter'),
        		'map'     => array($this, 'iteratorMap'),
            	'sort'    => $state->sort
        	));
        	if ($files === false) {
        		throw new UnexpectedValueException('Invalid folder');
        	}

            $this->_total = count($files);
            
            if (strtolower($this->_state->direction) == 'desc') {
            	$files = array_reverse($files);
            }
            
            $files = array_slice($files, $state->offset, $state->limit ? $state->limit : $this->_total);

            $data = array();
            foreach ($files as $file)
            {
                $data[] = array(
                	'container' => $state->container,
                	'folder' => $state->folder,
                	'name' => $file
                );
            }

            $this->_list = $this->getRowset(array(
                'data' => $data
            ));
        }

        return parent::getList();
    }

	public function iteratorMap($path)
	{
		return basename($path);
	}

	public function iteratorFilter($path)
	{
		$filename = basename($path);
		$extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

		if ($this->_state->name) {
			if (!in_array($filename, (array) $this->_state->name)) {
				return false;
			}
		}

		if ($this->_state->types) 
        {
			if ((in_array($extension, ComFilesDatabaseRowFile::$image_extensions) && !in_array('image', (array) $this->_state->types))
			|| (!in_array($extension, ComFilesDatabaseRowFile::$image_extensions) && !in_array('file', (array) $this->_state->types))
			) {
				return false;
			}
		}
		if ($this->_state->search && stripos($filename, $this->_state->search) === false) return false;
	}
}
