<?php
/**
 * Joomlatools Framework - https://www.joomlatools.com/developer/framework/
 *
 * @copyright	Copyright (C) 2011 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/joomlatools-framework-files for the canonical source repository
 */

/**
 * Attachment Controller
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Files
 */
class ComFilesControllerAttachment extends ComKoowaControllerModel
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->addCommandCallback('before.read' , '_serveFile');
    }

    protected function _initialize(KObjectConfig $config)
    {
        if ($this->getIdentifier()->package != 'files')
        {
            $aliases = array(
                'com:files.model.attachments'       => array(
                    'path' => array('model'),
                    'name' => 'attachments'
                ),
                'com:files.model.attachments_files' => array(
                    'path' => array('model'),
                    'name' => 'attachments_files'
                ),
                'com:files.template.helper.icon' => array(
                    'path' => array('template', 'helper'),
                    'name' => 'icon'
                ),
                'com:files.template.helper.uploader' => array(
                    'path' => array('template', 'helper'),
                    'name' => 'uploader'
                )
            );

            $manager = $this->getObject('manager');

            foreach ($aliases as $identifier => $alias)
            {
                $alias = array_merge($this->getIdentifier()->toArray(), $alias);

                if (!$manager->getClass($alias, false)) {
                    $manager->registerAlias($identifier, $alias);
                }
            }
        }

        parent::_initialize($config);
    }

    /**
     * Before Render command handler.
     *
     * Pushes permissions to the view.
     *
     * @param KControllerContextInterface $context The context object.
     */
    protected function _beforeRender(KControllerContextInterface $context)
    {
        $config = $this->getView()->getConfig();

        $config->merge(array(
            'can_add'    => $this->canAdd(),
            'can_delete' => $this->canDelete()
        ));

        if ($container = $this->getRequest()->getQuery()->container) {
            $config->container = $container;
        }
    }

    /**
     * Before add command handler.
     *
     * Makes sure that there's an attachment and that this attachment exists.
     *
     * @param KControllerContextInterface $context The context object.
     */
    protected function _beforeAdd(KControllerContextInterface $context)
    {
        if (!$context->file) {
            $context->file = $this->getModel()->getFilesModel()->fetch()->getIterator()->current();
        }


        if (!$context->file instanceof ComFilesModelEntityAttachments_file) {
            throw new RuntimeException('Attachment file missing');
        }
    }

    /**
     * Attach action.
     *
     * Creates a relationship between a resource and an existing attachment file.
     *
     * @param KControllerContextInterface $context The context object.
     */
    protected function _actionAdd(KControllerContextInterface $context)
    {
        // Set the file id within the attachment entry
        $context->getRequest()->getData()->{$context->file->getTable()->getIdentityColumn()} = $context->file->id;

        return parent::_actionAdd($context);
    }

    protected function _beforeDelete(KControllerContextInterface $context)
    {
        $attachments = $this->getModel()->fetch();

        $files = array();

        foreach ($attachments as $attachment) {
            $files[] = $attachment->file;
        }

        $context->files = $files;
    }

    protected function _afterDelete(KControllerContextInterface $context)
    {
        $model = $this->getModel();

        foreach ($context->files as $file)
        {
            $model->getState()->reset();

            if (!$model->file($file->id)->count())
            {
                if (!$file->delete()) {
                    throw new RuntimeException(('Attachment file could not be deleted'));
                }
            }
        }
    }

    protected function _serveFile(KControllerContextInterface $context)
    {
        $request = $context->getRequest();

        if ($request->isSafe() && $request->getFormat() == 'html')
        {
            $attachment = $this->getModel()->fetch();

            if (!$attachment->isNew())
            {
                $file = $attachment->file;

                $response = $this->getResponse();

                $this->getObject('com:files.controller.file')
                     ->name($file->name)
                     ->folder($file->path)
                     ->container($file->storage->container)
                     ->setResponse($response)
                     ->render();

                $response->send();
            }
        }
    }
}