<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-files for the canonical source repository
 */

/**
 * Containers Model
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
class ComFilesModelContainers extends KModelDatabase
{
	protected function _buildQueryWhere(KDatabaseQueryInterface $query)
	{
		parent::_buildQueryWhere($query);

        $state = $this->getState();

		if ($state->search) {
            $query->where('tbl.title LIKE :search')->bind(array('search' =>  '%'.$state->search.'%'));
        }
	}
}
