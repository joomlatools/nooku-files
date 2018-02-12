<?php
/**
 * Joomlatools Framework - https://www.joomlatools.com/developer/framework/
 *
 * @copyright	Copyright (C) 2011 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/joomlatools-framework-files for the canonical source repository
 */

/**
 * File Link Template Helper
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Files
 */
class ComFilesTemplateHelperLink extends KTemplateHelperAbstract
{
    public function attachment($config = array())
    {
        $config = new KObjectConfig($config);

        $config->append(array('file' => $config->attachment->file->storage));

        $file = $config->file;

        if ($file->isVideo()) {
            $html = $this->video($config);
        } elseif ($file->isAudio()) {
            $html = $this->audio($config);
        } elseif ($file->isImage()) {
            $html = $this->image($config);
        } else {
            $html = $this->link($config);
        }

        return $html;
    }

    public function video($config = array())
    {
        $config = new KObjectConfig($config);

        $config->append(array('layout' => 'com:files.file.video.html'));

        return $this->audio($config);
    }

    public function audio($config = array())
    {
        $config = new KObjectConfig($config);

        $file = $config->file;

        $config->append(array(
            'layout'     => 'com:files.file.audio.html',
            'url'        => sprintf('files://%s/%s', $file->container, $file->path),
            'attributes' => array(
                'data-category' => $this->getIdentifier()->getPackage(),
                'data-title'    => $file->name,
                'data-media-id' => 0
            )
        ));

        if ($config->token) {
            $config->url = $this->_token($config->url, $config->token);
        }

        $html = $this->getTemplate()->createHelper('behavior')->plyr($config);

        $attributes = $this->_prepareAttributes($config->attributes);

        $html .= $this->_render($config->layout, array(
            'url'        => $config->url,
            'file'       => $config->file,
            'attributes' => $attributes
        ));

        return $html;
    }

    public function image($config = array())
    {
        $config = new KObjectConfig($config);

        $file = $config->file;

        $config->append(array(
            'layout'     => 'com:files.file.image.html',
            'thumbnails' => false,
            'url'        => sprintf('files://%s/%s', $file->container, $file->path),
            'attributes' => array()
        ));

        if ($config->token) {
            $config->url = $this->_token($$config->url, $config->token);
        }

        $html = '';

        if ($file->isImage())
        {
            $attributes = array();

            if ($config->thumbnails && ($thumbnails = $file->getThumbnail()))
            {
                $srcset = array();

                if ($thumbnails->count() > 1)
                {
                    $container = $thumbnails->getIterator()->current()->getContainer();

                    foreach ($container->getParameters()->versions as $label => $settings)
                    {
                        if ($thumbnail = $thumbnails->find($label))
                        {
                            $src = $thumbnail->url ? $thumbnail->url : sprintf('files://%s/%s', $thumbnail->container, $thumbnail->path);

                            $srcset[$settings->dimension->width] = sprintf('%s %sw', $src, $settings->dimension->width);
                        }
                    }

                    if (count($srcset))
                    {
                        ksort($srcset, SORT_NUMERIC);

                        $srcset = array_values(array_reverse($srcset, true));
                    }

                    $attributes = $this->_prepareAttributes($config->attributes);
                }
            }

            $html = $this->_render($config->layout, array(
                'url'        => $config->url,
                'file'       => $file,
                'attributes' => $attributes,
                'srcset'     => $srcset
            ));
        }

        return $html;
    }

    public function link($config = array())
    {
        $config = new KObjectConfig($config);

        $file = $config->file;

        $config->append(array(
            'layout'     => 'com:files.file.link.html',
            'url'        => sprintf('files://%s/%s', $file->container, $file->path),
            'attributes' => array(),
            'text'      => $file->name
        ));

        if ($config->token) {
            $config->url = $this->_token($config->url, $config->token);
        }

        $attributes = $this->_prepareAttributes($config->attributes);

        return $this->_render($config->layout, array(
            'url'        => $config->url,
            'file'       => $file,
            'attributes' => $attributes,
            'text'       => $config->text
        ));

    }

    protected function _prepareAttributes($attributes)
    {
        $result = array();

        foreach ($attributes as $key => $value) {
            $result[] = sprintf('%s="%s"', $key, $value);
        }

        return $result;
    }

    protected function _token($url, $config = array())
    {
        $config = new KObjectConfig(array('name' => 'exp_token'));

        $stringify = false;

        if (!$url instanceof KHttpUrlInterface)
        {
            $url       = $this->getObject('lib:http.url', array('url' => $url));
            $stringify = true; // Original URL is a string, we should return a string
        }

        $url->setQuery(array($config->name, $this->token($config)));

        return $stringify ? $url->toString() : $url;
    }

    public function token($config = array())
    {
        $config = new KObjectConfig($config);

        $config->append(array('expire' => '+24 hours', 'secret' => ''));

        $token = $this->getObject('lib:http.token');

        $timezone = new DateTimeZone('UTC');

        $date = new DateTime('now', $timezone);

        $token->setExpireTime($date->modify($config->expire));

        return $token->sign($config->secret);
    }

    protected function _render($layout, $config = array())
    {
        return $this->getTemplate()
                    ->loadFile($layout)
                    ->render($config);
    }
}