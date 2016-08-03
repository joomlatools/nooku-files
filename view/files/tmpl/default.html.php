<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/joomlatools-framework-files for the canonical source repository
 */
defined('KOOWA') or die( 'Restricted access' ); ?>

<?= import('scripts.html');?>

<script>
    Files.sitebase = '<?= $sitebase; ?>';
    Files.token = '<?= $token; ?>';

    window.addEvent('domready', function() {
        var config = <?= json_encode(KObjectConfig::unbox(parameters()->config)); ?>,
            options = {
                cookie: {
                    path: '<?=object('request')->getSiteUrl()?>'
                },
                state: {
                    defaults: {
                        limit: <?= (int) parameters()->limit; ?>,
                        offset: <?= (int) parameters()->offset; ?>,
                        types: <?= json_encode(KObjectConfig::unbox(parameters()->types)); ?>
                    }
                },
                root_text: <?= json_encode(translate('Root folder')) ?>,
                types: <?= json_encode(KObjectConfig::unbox(parameters()->types)); ?>,
                container: <?= json_encode($container ? $container->toArray() : null); ?>,
                thumbnails: <?= json_encode($container ? $container->getParameters()->thumbnails : true); ?>
            };
        options = Object.append(options, config);

        Files.app = new Files.App(options);
    });
</script>

<!-- Component -->
<div class="k-component" id="files-app">

    <div class="k-flex-wrapper" id="files-canvas">

        <!-- Title when sidebar is invisible -->
        <div class="k-title-bar k-title-bar--mobile k-js-title-bar">
            <ktml:toolbar type="actionbar" no-buttons>
        </div>

        <!-- Scopebar -->
        <div class="k-scopebar k-js-scopebar">

            <!-- Breadcrumb -->
            <div class="k-scopebar__item k-scopebar__item--breadcrumbs">
                <div id="files-pathway" class="k-breadcrumb"></div>
            </div>

            <!-- Buttons -->
            <? // @TODO: Ercan: Doesn't seem to be working anymore even though I changed the JS as well; ?>
            <div class="k-scopebar__item k-scopebar__item--buttons">
                <button class="k-scopebar__button k-js-layout-switcher" data-layout="icons" title="<?= translate('Show files as icons'); ?>">
                    <span class="k-icon-grid-four-up" aria-hidden="true"></span>
                    <span class="k-visually-hidden">Grid icon</span>
                </button>
                <button class="k-scopebar__button k-js-layout-switcher" data-layout="details" title="<?= translate('Show files in a list'); ?>">
                    <span class="k-icon-list" aria-hidden="true"></span>
                    <span class="k-visually-hidden">List icon</span>
                </button>
            </div>

            <!-- Search -->
            <div class="k-scopebar__item k-scopebar__item--search">
                <?= helper('grid.search', array('submit_on_clear' => false, 'placeholder' => @translate('Find by file or folder name&hellip;'))) ?>
            </div>

        </div><!-- .k-scopebar -->

        <? if (!isset(parameters()->config->can_upload) || parameters()->config->can_upload): ?>
            <?= import('uploader.html');?>
        <? endif; ?>

        <div class="k-flex-wrapper">
            <div id="files-grid-container">
                <div id="files-grid"></div>
                <div class="k-table-pagination" id="files-paginator-container">
                    <?= helper('paginator.pagination') ?>
                </div>
            </div>
        </div>

    </div><!-- .k-flex-wrapper -->

</div><!-- .k-component -->


<div class="k-dynamic-content-holder">
    <?= import('templates_icons.html'); ?>
    <?= import('templates_details.html'); ?>

    <div id="files-new-folder-modal" class="koowa mfp-hide" style="max-width: 600px; position: relative; width: auto; margin: 20px auto;">
        <form class="files-modal well">
            <div style="text-align: center;">
                <h3 style=" float: none">
                    <?= translate('Create a new folder in {folder}', array(
                        'folder' => '<span class="upload-files-to"></span>'
                    )) ?>
                </h3>
            </div>
            <div class="input-append">
                <input class="span5 focus" type="text" id="files-new-folder-input" placeholder="<?= translate('Enter a folder name') ?>" />
                <button id="files-new-folder-create" class="btn btn-primary" disabled><?= translate('Create'); ?></button>
            </div>
        </form>
    </div>

    <div id="files-move-modal" class="koowa mfp-hide" style="max-width: 600px; position: relative; width: auto; margin: 20px auto;">
        <form class="files-modal well">
            <div>
                <h3><?= translate('Move to') ?></h3>
            </div>
            <div class="tree-container"></div>
            <div class="form-actions" style="padding-left: 0">
                <button class="btn btn-primary" ><?= translate('Move'); ?></button>
            </div>
        </form>
    </div>

    <div id="files-copy-modal" class="koowa mfp-hide" style="max-width: 600px; position: relative; width: auto; margin: 20px auto;">
        <form class="files-modal well">
            <div>
                <h3><?= translate('Copy to') ?></h3>
            </div>
            <div class="tree-container"></div>
            <div class="form-actions" style="padding-left: 0">
                <button class="btn btn-primary" ><?= translate('Copy'); ?></button>
            </div>
        </form>
    </div>
</div>