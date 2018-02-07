<?php
/**
 * Joomlatools Framework - https://www.joomlatools.com/developer/framework/
 *
 * @copyright	Copyright (C) 2011 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/joomlatools-framework-files for the canonical source repository
 */

/**
 * Attachment Controller Behavior
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Files
 */
class ComFilesControllerBehaviorAttachment extends KControllerBehaviorAbstract
{
    /**
     * Attachment Controller.
     *
     * @var KControllerInterface|null
     */
    protected $_controller;

    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_controller = $config->controller;
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array('controller' => 'attachment'));

        parent::_initialize($config);
    }

    /**
     * After Add command handler.
     *
     * Creates an attachments file entity.
     *
     * @param KControllerContextInterface $context The context object.
     */
    protected function _afterAdd(KControllerContextInterface $context)
    {
        $entity = $context->result;

        if ($entity instanceof ComFilesModelEntityNode && $entity->getStatus() !== KModelEntityInterface::STATUS_FAILED)
        {
            $model     = $this->_getController()->getModel()->getFilesModel();
            $container = $entity->getContainer();

            $folder = $entity->folder ?: '.';

            $file = $model
                ->name($entity->name)
                ->path($folder)
                ->container($container->id)
                ->fetch();

            if ($file->isNew())
            {
                $file = $model->create(array('name' => $entity->name, 'path' => $folder));
                $file->save();
            }
            else $file = $file->getIterator()->current();

            $data = $context->getRequest()->getData();

            // See if we should create the attachment
            if ($data->table && $data->row)
            {
                $controller = $this->_getController();

                $attachment_context = $controller->getContext();
                $attachment_context->file = $file;

                $attachment_context->getRequest()->setData(array(
                    'table' => 'content',
                    'row'   => $data->row,
                ));

                $entity->attachment = $controller->add($attachment_context);
            }
        }
    }

    /**
     * Attachment Controller getter.
     *
     * @return KControllerInterface
     */
    protected function _getController()
    {
        if (!$this->_controller instanceof KControllerInterface)
        {
            if (!$this->_controller instanceof KObjectIdentifierInterface)
            {
                if (strpos($this->_controller, '.') === false)
                {
                    $parts         = $this->getIdentifier()->toArray();
                    $parts['path'] = array('controller');
                    $parts['name'] = $this->_controller;

                    $identifier = $this->getIdentifier($parts);
                } else $identifier = $this->getIdentifier($this->_controller);
            } else $identifier = $this->_controller;

            $this->_controller = $this->getObject($identifier);

            // Forward thumbnails state to the attachment controller
            if ($thumbnails = $this->getMixer()->getRequest()->getQuery()->thumbnails) {
                $this->_controller->getRequest()->getQuery()->thumbnails = $thumbnails;
            }
        }

        return $this->_controller;
    }
}