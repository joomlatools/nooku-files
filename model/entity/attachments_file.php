<?php
/**
 * Joomlatools Framework - https://www.joomlatools.com/developer/framework/
 *
 * @copyright	Copyright (C) 2011 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/joomlatools-framework-files for the canonical source repository
 */

/**
 * Attachments File Model Entity
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Files
 */
class ComFilesModelEntityAttachments_file extends KModelEntityRow
{
    protected $_file;

    protected $_container;

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array('thumbnails' => false));

        parent::_initialize($config);
    }

    public function getPropertyStorage()
    {
        if (!$this->_file instanceof ComFilesModelEntityFile)
        {
            $model = $this->getObject('com:files.model.files');

            if ($thumbnails = $this->getConfig()->thumbnails) {
                $model->thumbnails($thumbnails);
            }

            $file = $model->container($this->container_slug)
                  ->name($this->name)
                  ->folder($this->path)
                  ->fetch();

            if (!$file->isNew()) {
                $this->_file = $file->getIterator()->current();;
            }
        }

        return $this->_file;
    }

    public function getPropertyContainerSlug()
    {
        $slug = null;

        $container = $this->container;

        if (!$container instanceof ComFilesModelEntityContainer)
        {
            $container = $this->getObject('com:files.model.containers')
                              ->id($container)
                              ->fetch();

            if (!$container->isNew())
            {
                $this->_container = $container->getIterator()->current();
                $slug             = $this->_container->slug;
            }
        }
        else $slug = $$container->slug;

        return $slug;
    }

    public function reset()
    {
        parent::reset();

        $this->_container = null;
        $this->_file      = null;
    }

    public function delete()
    {
        if ($result = parent::delete()) {
            $this->storage->delete();
        }

        return $result;
    }

    public function toArray()
    {
        $data = parent::toArray();

        if ($storage = $this->storage)
        {
            $data['storage']                  = $storage->toArray();
            $data['storage']['relative_path'] = $storage->relative_path;
        }

        return $data;
    }
}