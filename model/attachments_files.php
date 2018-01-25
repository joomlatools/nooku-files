<?php
/**
 * Joomlatools Framework - https://www.joomlatools.com/developer/framework/
 *
 * @copyright	Copyright (C) 2011 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/joomlatools-framework-files for the canonical source repository
 */

/**
 * Attachments Model
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Files
 */
class ComFilesModelAttachments_files extends KModelDatabase
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->getState()
             ->insert('name', 'string', null, true, array('container', 'path'))
             ->insert('container', 'int', null, true, array('name', 'path'))
             ->insert('path', 'string', null, true, array('name', 'container'))
             ->insert('thumbnails', 'raw', null, false, array(), true);
    }

    protected function _buildQueryColumns(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryColumns($query);

        $query->columns(array('tbl.*', 'container_slug' => 'containers.slug'));
    }

    protected function _buildQueryJoins(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryJoins($query);

        $query->join('files_containers AS containers', 'containers.files_container_id = tbl.files_container_id', 'INNER');
    }

    protected function _afterFetch(KModelContext $context)
    {
        foreach ($context->entity as $entity)
        {
            if ($thumbnails = $this->getState()->thumbnails) {
                $entity->getConfig()->append(array('thumbnails' => $thumbnails));
            }
        }
    }

    /**
     * Overridden for pushing the container value.
     */
    protected function _actionCreate(KModelContext $context)
    {
        $context->entity->append(array(
            'container' => $context->state->container,
            'path'      => '.'
        ));

        $entity = parent::_actionCreate($context);

        if ($thumbnails = $this->getState()->thumbnails) {
            $entity->getConfig()->append(array('thumbnails' => $thumbnails));
        }

        return $entity;
    }
}