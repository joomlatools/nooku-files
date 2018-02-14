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
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
class ComFilesViewJson extends KViewJson
{
    protected $_filter;

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array('filter' => 'com:files.template.filter.url'));
        parent::_initialize($config);
    }

    protected function _renderData()
    {
        $output = parent::_renderData();

        if (!$this->isCollection())
        {
            $entity    = $this->getModel()->fetch();
            $status = $entity->getStatus() !== KDatabase::STATUS_FAILED;

            $output['status'] = $status;

            if ($status === false){
                $output['error'] = $entity->getStatusMessage();
            }
        }

        return $output;
    }

    /**
     * Converts links in an array from relative to absolute
     *
     * @param array $array Source array
     */
    protected function _processLinks(array &$array)
    {
        $base = $this->getUrl()->toString(KHttpUrl::AUTHORITY);

        foreach ($array as $key => &$value)
        {
            if (is_array($value)) {
                $this->_processLinks($value);
            }
            elseif ($key === 'href')
            {
                if (substr($value, 0, 4) !== 'http') {
                    $array[$key] = $base.$value;
                }
            }
            elseif ($key === 'uri')
            {
                // Expose URL from URI using template filter
                $parts = explode('://', $value);
                $url = sprintf('files://%s/%s', $parts[0], $parts[1]);
                $this->_getFilter()->filter($url);
                $array['url'] = $url;
            }
            elseif (in_array($key, $this->_text_fields)) {
                $array[$key] = $this->_processText($value);
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
