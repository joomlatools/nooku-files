<?php
/**
 * Joomlatools Framework - https://www.joomlatools.com/developer/framework/
 *
 * @copyright	Copyright (C) 2011 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/joomlatools-framework-files for the canonical source repository
 */

/**
 * Includable Dispatcher Behavior
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Files
 */
class ComFilesDispatcherBehaviorIncludable extends KControllerBehaviorAbstract
{
    protected function _beforeInclude(KDispatcherContextInterface $context)
    {
        $context->append(array('param' => array('dispatcher' => $this->getMixer()->getIdentifier())));
    }

    /**
     * Include the request
     *
     * Dispatch to a controller internally and handles back the context without sending the response
     *
     * @param DispatcherContext $context A dispatcher context object
     * @return  mixed
     */
    protected function _actionInclude(KDispatcherContextInterface $context)
    {
        $param = $context->param;

        if (is_string($param) || $param instanceof KObjectIdentifierInterface) {
            $param = new KObjectConfig(array('dispatcher' => $param));
        }

        $identifier = $param->dispatcher;

        //Get the dispatcher identifier
        if(is_string($identifier) && strpos($identifier, '.') === false )
        {
            $identifier            = $this->getIdentifier()->toArray();
            $identifier['package'] = $identifier;
        }

        //Create the dispatcher
        $config = array(
            'request'  => clone $context->request,
            'response' => $context->response,
            'user'     => $context->user,
        );

        $dispatcher = $this->getObject($identifier, $config);

        if ($query = $param->query)
        {
            if ($query instanceof KHttpMessageParameters) {
                $query = $query->toArray();
            }

            if (!is_array($query)) throw new RuntimeException('Includable Dispatcher Behavior: $query should be an array');

            $dispatcher->getRequest()->getQuery()->clear()->add($query);
        }

        if(!$dispatcher instanceof KDispatcherInterface)
        {
            throw new UnexpectedValueException(
                'Dispatcher: '.get_class($dispatcher).' does not implement KDispatcherInterface'
            );
        }

        $context = $dispatcher->getContext();

        $context->included = true;

        return $dispatcher->dispatch($context);
    }

    protected function _beforeSend(KDispatcherContextInterface $context)
    {
        if ($context->included)
        {
            if ($result = $context->result) {
                $context->included_result = $result; // Keep a copy of the result
            }

            return false;
        }
    }

    protected function _afterDispatch(KDispatcherContextInterface $context)
    {
        if ($context->included && is_null($context->result) && $context->included_result) {
            $context->result = $context->included_result;
        }
    }
}