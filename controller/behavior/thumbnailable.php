<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/joomlatools-framework-files for the canonical source repository
 */

/**
 * Thumbnailable Controller Behavior
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Files
 */
class ComFilesControllerBehaviorThumbnailable extends KControllerBehaviorAbstract
{
    protected function _getContainer()
    {
        return $this->getModel()->getThumbnailsContainer();
    }

    protected function _beforeMove(KControllerContextInterface $context)
    {
        $entities = $this->getModel()->fetch();

        $source_folders = array();

        foreach ($entities as $entity) {
            $source_folders[$entity->name] = $entity->folder;
        }

        if (!empty($source_folders)) {
            $context->source_folders = $source_folders;
        }
    }

    protected function _afterMove(KControllerContextInterface $context)
    {
        $entities = $context->result;

        if ($source_folders = $context->source_folders)
        {
            foreach ($entities as $entity)
            {
                $file = $this->_getFile($entity);

                if ($source_folders[$file->name])
                {
                    $file->folder = $source_folders[$file->name];

                    $thumbnails = $this->getObject('com:files.model.thumbnails')
                                       ->source($file->uri)
                                       ->container($this->_getContainer()->slug)->fetch();

                    foreach ($thumbnails as $thumbnail)
                    {
                        $thumbnail->destination_folder = $entity->destination_folder;
                        $thumbnail->destination_name   = $thumbnail->name;

                        $thumbnails->{$context->getAction()}();
                    }
                }
            }
        }
    }

    protected function _beforeCopy(KControllerContextInterface $context)
    {
        $this->_beforeMove($context);
    }

    protected function _afterCopy(KControllerContextInterface $context)
    {
        $this->_afterMove($context);
    }

    protected function _getFile(KModelEntityInterface $entity)
    {
        return $entity;
    }

    protected function _afterDelete(KControllerContextInterface $context)
    {
        $entities = $context->result;

        foreach ($entities as $entity)
        {
            $file = $this->_getFile($entity);

            $controller = $this->getObject('com:files.controller.thumbnail')
                               ->container($this->_getContainer()->slug)
                               ->source($file->uri);

            $parameters = $this->_getContainer()->getParameters();

            if ($versions = $parameters->versions) {
                $controller->version(array_keys($versions->toArray()));
            }

            $thumbnails = $controller->browse();

            if ($thumbnails->count()) {
                $thumbnails->delete();
            }
        }
    }
}