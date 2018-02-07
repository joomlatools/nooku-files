<?php
/**
 * Joomlatools Framework - https://www.joomlatools.com/developer/framework/
 *
 * @copyright	Copyright (C) 2011 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/joomlatools-framework-files for the canonical source repository
 */
defined('KOOWA') or die;
?>

<?= helper('ui.load', array('wrapper_class' => array('com_files--attachments'))); ?>

<?= import('com:files.files.scripts.html'); ?>

<ktml:script src="media://koowa/com_files/js/files.attachments.js"/>
<ktml:style src="media://koowa/com_files/css/files.css"/>

<div class="k-dynamic-content-holder">
    <script>
        Files.sitebase = '<?= $sitebase; ?>';
        Files.token = '<?= $token; ?>';

        kQuery(function($)
        {
            var config = <?= json_encode(KObjectConfig::unbox(parameters()->config)); ?>,
                options = {

                    cookie: {path: '<?=object('request')->getSiteUrl()?>'},
                    callback: <?= json_encode(isset($callback) ? $callback : '') ?>,
                    url:  "<?= route('component='. urlencode($component) .'&view=attachments&format=json&table=' . $table . '&row=' . $row, true, false) ?>",
                    root_text: <?= json_encode(translate('Root folder')) ?>,
                    editor: <?= json_encode(parameters()->editor); ?>,
                    types: <?= json_encode(KObjectConfig::unbox(parameters()->types)); ?>,
                    container: <?= json_encode($container->toArray()) ?>
                };
            options = Object.append(options, config);

            Files.app = new Files.Attachments.App(options);

            var app = Files.app;

            var updateGridCount = function() {
                $('#document_list .count').html('(' + this.getCount() + ')');
            }.bind(app.grid);

            // Update attachements label count.
            app.grid.addEvent('afterInsertRows', function() {
                updateGridCount();
            });

            // Update attachements label count.
            app.grid.addEvent('afterDeleteNode', function() {
                updateGridCount();
            });

            app.grid.addEvent('afterRenderObject', function(object, position)
            {
                var that = this;

                $(object.object.element).find('span').click(function()
                {
                    var attachment = object.object.name;
                    that.select(attachment);

                    if (confirm(<?= json_encode(translate('You are about to remove this attachment. Would you like to proceed?')) ?>))
                    {
                        node = this.nodes.get(attachment);

                        if (node) {
                            Attachments.delete(attachment);
                        }
                    }
                });
            }.bind(app.grid));

            app.grid.addEvent('afterInsertNode', function(data)
            {
                this.select(data.node); // Auto-select attached file after attach.

            }.bind(app.grid));

            Attachments = Attachments.getInstance(
                {
                    url: "<?= route('component=' . urlencode($component) . '&view=attachment', true, false) ?>",
                    selector: '#document_list',
                    csrf_token: <?= json_encode(object('user')->getSession()->getToken()) ?>
                }
            );

            $('.attachments-uploader').on('uploader:uploaded', function (event, data)
            {
                var response = data.result.response;

                if (typeof response.entities !== 'undefined')
                {
                    var entity = response.entities.pop();

                    this.insertRows(response.entities);
                    this.fireEvent('afterAddAttachment', {attachment: {name: entity.attachment.name, entity: entity}});

                    this.attach(data.file.name);
                }
            }).on('uploader:create', function() {
                $(this).addClass('k-upload--boxed-top');
            }).bind(app.grid)

            // Attach action implementation
            app.grid.attach = function (attachment)
            {
                this.fireEvent('beforeAttachAttachment', {attachment: attachment});
                Attachments.attach(attachment);
            }.bind(app.grid);

            Attachments.bind('after.delete', function (event, context)
            {
                this.erase(context.attachment);

                $('#files-preview').empty();
            }.bind(app.grid));
        });
    </script>

    <?= import('com:files.files.templates_compact.html');?>
    <?= import('com:files.attachments.templates_manage.html', array('can_delete' => $can_delete));?>
</div>

<!-- Wrapper -->
<div class="k-wrapper k-js-wrapper">

    <!-- Titlebar -->
    <div class="k-title-bar k-title-bar--mobile k-js-title-bar">
        <div class="k-title-bar__heading"><?= translate('Attachments'); ?></div>
    </div><!-- .k-titlebar -->

    <!-- Overview -->
    <div class="k-content-wrapper">

        <!-- Content -->
        <div class="k-content k-js-content">

            <!-- Component wrapper -->
            <div class="k-component-wrapper">

                <!-- Component -->
                <div class="k-component k-js-component">
                <div class="k-component k-js-component">

                    <!-- Uploader -->
                    <? if ($can_add): ?>
                        <div class="attachments-upload">
                            <?= helper('uploader.container', array(
                                'container' => $container->slug,
                                'element'   => '.attachments-uploader',
                                'options'   => array(
                                    'multi_selection'  => true,
                                    'duplicate_mode'   => $check_duplicates,
                                    'url'              => $uploader_url,
                                    'multipart_params' => array(
                                        'table'  => $table,
                                        'row'    => $row,
                                        'folder' => $folder
                                    )
                                )
                            )) ?>
                        </div>
                    <? endif ?>

                    <!-- Attachments list -->
                    <div class="k-table-container">
                        <div class="k-table" id="attachments-container"></div><!-- .k-table -->
                        <div class="k-loader-container">
                            <span class="k-loader k-loader--large"><?= translate('Loading') ?></span>
                        </div>
                    </div><!-- .k-table-container -->

                </div><!-- .k-component -->

                <!-- Sidebar -->
                <div class="k-sidebar-right k-js-sidebar-right">

                    <div class="k-sidebar-item">

                        <div class="k-sidebar-item__header">
                            <?= translate('Selected attachment info'); ?>
                        </div>

                        <div class="k-sidebar-item__content" id="properties">

                            <div id="attachments-preview">
                                <div id="files-preview"></div>
                            </div>

                        </div><!-- .k-sidebar__content -->

                    </div><!-- .k-sidebar__item -->

                </div><!-- .k-sidebar-right -->

            </div><!-- .k-component-wrapper -->

        </div><!-- k-content -->

    </div><!-- .k-content-wrapper -->

</div><!-- .k-wrapper -->