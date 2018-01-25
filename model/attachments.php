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
class ComFilesModelAttachments extends KModelDatabase
{
    protected $_files_model;

    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->getState()->insert('file', 'int')
             ->insert('path', 'string')
             ->insert('name', 'string')
             ->insert('thumbnails', 'raw', null, false, array(), true);

        $this->_files_model = $config->files_model;
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array('files_model' => 'attachments_files'));

        parent::_initialize($config);
    }

    protected function _buildQueryWhere(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryWhere($query);

        $state = $this->getState();

        if (!$state->isUnique())
        {
            if ($table = $state->table) {
                $query->where('tbl.table = :table')->bind(array('table' => $table));
            }

            if ($row = $state->row) {
                $query->where('tbl.row = :row')->bind(array('row' => $row));
            }

            if ($path = $state->path) {
                $query->where('files.path = :path')->bind(array('path' => $path));
            }

            if ($name = $state->name) {
                $query->where('files.name = :name')->bind(array('name' => $name));
            }

            if ($file = $state->file) {
                $query->where(sprintf('tbl.%s = :file', $this->getFilesModel()->getTable()->getIdentityColumn()))
                      ->bind(array('file' => $file));
            }
        }
    }

    protected function _buildQueryColumns(KDatabaseQueryInterface $query)
    {
        $query->columns(array(
            'files.*',
            'attached_by'      => 'tbl.created_by',
            'attached_on'      => 'tbl.created_on',
            'attached_by_name' => 'users.name'
        ));

        parent::_buildQueryColumns($query);
    }

    protected function _buildQueryJoins(KDatabaseQueryInterface $query)
    {
        $table = $this->getFilesModel()->getTable();

        // Join files table
        $query->join(sprintf('%s AS files', $table->getName()), sprintf('tbl.%1$s = files.%1$s', $table->getIdentityColumn()), 'INNER');

        // Join users table
        $query->join('users AS users', 'tbl.created_by = users.id', 'LEFT');

        parent::_buildQueryJoins($query);
    }

    public function getFilesModel()
    {
        $model = $this->_files_model;

        if (!$model instanceof KModelDatabase)
        {
            if(is_string($model) && strpos($model, '.') === false )
            {
                $identifier         = $this->getIdentifier()->toArray();
                $identifier['path'] = array('model');
                $identifier['name'] = KStringInflector::pluralize(KStringInflector::underscore($model));

                $model = $this->getIdentifier($identifier);
            }

            $model = $this->getObject($model);

            if (!$model instanceof KModelDatabase) {
                throw new UnexpectedValueException('Identifier: ' . $model . ' is not a database model identifier');
            }

            $state = $this->getState();

            if ($file = $state->file) {
                $model->id($file);
            }

            $this->_files_model = $model;
        }

        return $model;
    }

    protected function _afterFetch(KModelContext $context)
    {
        $identifier = $this->getFilesModel()->getIdentifier();

        foreach ($context->entity as $entity) {
            $entity->files_model = $identifier;
        }
    }

    /**
     * Overridden for pushing the container value.
     */
    protected function _actionCreate(KModelContext $context)
    {
        $context->entity->append(array(
            'files_model' => $this->getFilesModel()->getIdentifier(),
        ));

        return parent::_actionCreate($context);
    }
}