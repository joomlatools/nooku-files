<?php
/**
 * Joomlatools Framework - https://www.joomlatools.com/developer/framework/
 *
 * @copyright	Copyright (C) 2011 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/joomlatools-framework-files for the canonical source repository
 */
?>

<?= helper('ui.load', array('domain' => 'site')) ?>

<ktml:script src="assets://files/js/files.gallery.js"/>

<?= helper('behavior.modal') ?>

<script>
    kQuery(function($) {
        $('.attachments').simpleGallery();
    });
</script>

<? if (count($attachments)): ?>
    <div style="clear: both">
        <? if ($show_header): ?>
            <h3><?= translate('Attachments') ?></h3>
        <? endif ?>
        <div class="koowa_media--gallery">
            <div class="attachments koowa_media_wrapper koowa_media_wrapper--documents">
                <div class="koowa_media_contents">
                    <?php // this comment below must stay ?>
                    <div class="koowa_media"><!--
                    <? foreach ($attachments as $attachment): ?>
                        <?= import("com:files.attachment.gallery.html", array('attachment' => $attachment)) ?>
                    <? endforeach ?>
                --></div>
                </div>
            </div>
        </div>
    </div>
    <script>
        kQuery(function($) {
            $('.koowa_media__item__image').closest('a').magnificPopup({
                type: 'image',
                gallery:{
                    enabled:true
                }
            });
        });
    </script>
<? endif ?>


