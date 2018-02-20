<?php
/**
 * Joomlatools Framework - https://www.joomlatools.com/developer/framework/
 *
 * @copyright	Copyright (C) 2011 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/joomlatools-framework-files for the canonical source repository
 */

/**
 * Attachable Dispatcher Behavior
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Files
 */
class ComFilesDispatcherBehaviorAttachable extends KControllerBehaviorAbstract
{
    /**
     * The attachments container slug.
     *
     * @var string
     */
    protected $_container;

    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_container = $config->container;
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'container' => sprintf('%s-attachments',
                $config->mixer->getIdentifier()->getPackage())
        ));

        parent::_initialize($config);
    }

    protected function _attachBehaviors(KControllerContextInterface $context)
    {
        // For convenience when extending behavior
    }

    /**
     * Before Dispatch command handler.
     *
     * Makes sure to forward requests to com_files or set container data to the request depending on the view.
     *
     * @param KDispatcherContextInterface $context The context object.
     *
     * @return bool True if the request should be dispatched, false otherwise.
     */
    protected function _beforeDispatch(KDispatcherContextInterface $context)
    {
        $query = $context->getRequest()->getQuery();

        if (in_array($query->view, array('attachment', 'attachments')))
        {
            $this->_setAliases();
            $this->_attachBehaviors($context);

            $dispatcher = $context->getSubject();

            if (!$dispatcher->isIncludable()) {
                $dispatcher->addBehavior('com:files.dispatcher.behavior.includable');
            }

            if (!$query->container) {
                $query->container = $this->_container;
            }

            if ($query->plupload)
            {
                $result = $this->_upload($context);

                if (($attachment = $result->attachment) && !$attachment->isNew())
                {
                    $this->getResponse()->setContent($this->setController('attachment')
                                                          ->getController()
                                                          ->id($attachment->id)
                                                          ->render())->send();
                }
            }
        }
    }

    /**
     * Alias setter.
     */
    protected function _setAliases()
    {
        $mixer = $this->getMixer();

        $aliases = array(
            'com:files.controller.permission.attachment' => array(
                'path' => array('controller', 'permission'),
                'name' => 'attachment'
            ),
            'com:files.controller.behavior.attachment'   => array(
                'path' => array('controller', 'behavior'),
                'name' => 'attachment'
            ),
            'com:files.controller.attachment'            => array(
                'path' => array('controller'),
                'name' => 'attachment'
            )
        );

        $manager = $this->getObject('manager');

        foreach ($aliases as $identifier => $alias)
        {
            $alias = array_merge($mixer->getIdentifier()->toArray(), $alias);

            if (!$manager->getClass($alias, false)) {
                $manager->registerAlias($identifier, $alias);
            }
        }
    }

    /**
     * Forwards the request to com_files.
     *
     * @param KDispatcherContextInterface $context The context object.
     */
    protected function _upload(KDispatcherContextInterface $context)
    {
        $mixer = $this->getMixer();

        $parts = $mixer->getIdentifier()->toArray();
        $parts['path'] = array('controller', 'permission');
        $parts['name'] = 'attachment';

        $permission = $this->getIdentifier($parts)->toString();

        $parts['path'] = array('controller', 'behavior');

        $behavior = $this->getIdentifier($parts)->toString();

        $parts['path'] = array('controller');

        $controller = $this->getIdentifier($parts)->toString();

        // Set controller on attachment behavior and push attachment permission to file controller.
        $this->getIdentifier('com:files.controller.file')
             ->getConfig()
             ->append(array('behaviors' => array($behavior => array('controller' => $controller), 'permissible' => array('permission' => $permission))));

        $query = clone $context->getRequest()->getQuery();

        $query->view = 'file';

        $context->append(array(
            'param' => array(
                'query'      => $query,
                'dispatcher' => 'com:files.dispatcher.http',
            )
        ));

        $result = $this->include($context);

        unset($context->param);

        return $result;
    }
}