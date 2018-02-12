<?php
/**
 * Joomlatools Framework - https://www.joomlatools.com/developer/framework/
 *
 * @copyright	Copyright (C) 2011 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/joomlatools-framework-files for the canonical source repository
 */
?>

<? $url = isset($url) ?: $attachment->url ?>
<? $size = isset($size) ?: null ?>

<? if ($file = $attachment->file): ?>
<div class="koowa_media__item">
    <div class="koowa_media__item__content file">
        <a class="koowa_media__item__link" href="<?= route($url, true, false) ?>">
            <div class="koowa_media__item__content-holder">
                <? if ($file->storage->isImage()): ?>
                    <div class="koowa_media__item__image">
                        <? if ($file->storage->isThumbnailable() && ($thumbnail = $file->storage->getThumbnail($size))): ?>
                            <div class="koowa_media__item__thumbnail">
                                <img src="<?= sprintf('files://%s/%s', $thumbnail->container, $thumbnail->path) ?>"/>
                            </div>
                        <? else: ?>
                            <div class="koowa_media__item__icon">
                                <span class="k-icon-document-image k-icon--size-xlarge"></span>
                            </div>
                        <? endif ?>
                    </div>
                <? else: ?>
                    <div class="koowa_media__item__icon">
                        <span class="k-icon-document-default k-icon--size-xlarge"></span>
                    </div>
                <? endif ?>
                <div class="koowa_header koowa_media__item__label">
                    <div class="koowa_header__item koowa_header__item--title_container">
                        <div class="koowa_wrapped_content">
                            <div class="whitespace_preserver">
                                <div class="overflow_container">
                                    <span class="js-gallery-caption" style="display: none">
                                        <?= escape($file->name) ?>
                                    </span>
                                    <?= escape($file->name) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>
<? endif ?>
