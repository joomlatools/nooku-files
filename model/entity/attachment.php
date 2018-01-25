<?php
/**
 * Joomlatools Framework - https://www.joomlatools.com/developer/framework/
 *
 * @copyright	Copyright (C) 2011 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/joomlatools-framework-files for the canonical source repository
 */

/**
 * Attachment Model Entity
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Files
 */
class ComFilesModelEntityAttachment extends KModelEntityRow
{
    protected $_file = null;

    /**
     * Attachment file getter.
     *
     * @return KModelEntityInterface
     */
    public function getPropertyFile()
    {
        if (!$this->_file instanceof ComFilesModelEntityAttachments_file && !$this->isNew() && ($model = $this->files_model))
        {
            $model = $this->getObject($this->files_model);

            if ($thumbnails = $this->getConfig()->thumbnails) {
                $model->thumbnails($thumbnails);
            }

            $key = $model->getTable()->getIdentityColumn();

            $file = $model->id($this->{$key})->fetch();

            if (!$file->isNew()) {
                $this->_file = $file->getIterator()->current();;
            }
        }

        return $this->_file;
    }

    public function toArray()
    {
        $data = parent::toArray();

        $file = $this->file;

        if ($file && !$file->isNew()) {
            $data['file'] = $file->toArray();
        }

        $data['created_on_timestamp']  = strtotime($this->created_on);
        $data['attached_on_timestamp'] = strtotime($this->attached_on);

        return $data;
    }

    public function reset()
    {
        parent::reset();

        $this->_file = null;
    }
}