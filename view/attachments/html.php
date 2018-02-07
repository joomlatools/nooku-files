<?php
/**
 * Joomlatools Framework - https://www.joomlatools.com/developer/framework/
 *
 * @copyright	Copyright (C) 2011 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/joomlatools-framework-files for the canonical source repository
 */

/**
 * Attachments Html View
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
class ComFilesViewAttachmentsHtml extends ComKoowaViewHtml
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'auto_fetch' => false,
            'container'  => 'attachments',
            'can_add'    => false,
            'can_delete' => false,
            'thumbnails' => false
        ));

        parent::_initialize($config);
    }

    protected function _fetchData(KViewContext $context)
    {
        $config = $this->getConfig();
        $state  = $this->getModel()->getState();
        $query  = $this->getUrl()->getQuery(true);

        $container = $this->getObject('com:files.model.containers')->slug($this->getConfig()->container)->fetch();

        $folder = sprintf('%s/%s', $state->table, $state->row);

        $url_format = 'view=attachment&plupload=1&format=json&container=%s&thumbnails=%s';

        $uploader_url = $this->getRoute(sprintf($url_format, $container->slug, $config->thumbnails), false, false);

        $context->data->sitebase         = trim(JURI::root(), '/');
        $context->data->token            = $this->getObject('user')->getSession()->getToken();
        $context->data->container        = $container->getIterator()->current();
        $context->data->can_add          = $config->can_add;
        $context->data->can_delete       = $config->can_delete;
        $context->data->check_duplicates = $container->getParameters()->check_duplicates ?: 'unique';
        $context->data->callback         = isset($query['callback']) ? $query['callback'] : null;
        $context->data->row              = $state->row;
        $context->data->table            = $state->table;
        $context->data->folder           = $folder;
        $context->data->uploader_url     = $uploader_url;

        parent::_fetchData($context);
    }
}