<?php
/**
 * Joomlatools Framework - https://www.joomlatools.com/developer/framework/
 *
 * @copyright	Copyright (C) 2011 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/joomlatools-framework-files for the canonical source repository
 */

/**
 * Json View
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Files
 */
class ComFilesViewBehaviorRoutable extends KViewBehaviorAbstract
{
    protected $_filter;

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array('filter' => 'com:files.template.filter.url', 'key' => 'url'));

        parent::_initialize($config);
    }

    public function isSupported()
    {
        return $this->getMixer()->getFormat() == 'json';
    }

    protected function _afterRender(KViewContextInterface $context)
    {
        $data = json_decode($context->result);

        if (isset($data->entities))
        {
            foreach ($data->entities as $entity) {
                $this->_scan($entity);
            }
        }

        $this->getCommandChain()->disable();

        $this->setContent($data);

        $this->render();

        $this->getCommandChain()->enable();

        $context->result = $this->getContent();
    }

    protected function _scan($data, $property = null)
    {
        foreach ($data as $key => $value)
        {
            if (is_object($value))
            {
                $this->_scan($value, $key);
            }
            elseif ($key === 'uri' && !isset($data->{$this->getConfig()->key}))
            {
                $parts = explode('://', $value);
                $uri   = sprintf('files://%s/%s', $parts[0], $parts[1]);

                $this->_getFilter()->filter($uri);

                $data->{$this->getConfig()->key} = $uri;
            }
        }
    }

    protected function _getFilter()
    {
        if (!$this->_filter) {
            $this->_filter = $this->getObject($this->getConfig()->filter);
        }

        return $this->_filter;
    }
}

