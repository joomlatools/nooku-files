<?php
/**
 * @version     $Id$
 * @category	Nooku
 * @package     Nooku_Server
 * @subpackage  Files
 * @copyright   Copyright (C) 2011 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */
defined('KOOWA') or die( 'Restricted access' ); ?>

<?= @template('initialize');?>

<script>
Files.sitebase = '<?= $sitebase; ?>';
Files.token = '<?= $token; ?>';

window.addEvent('domready', function() {
	var config = <?= json_encode($state->config); ?>,
		options = {
			state: {
				defaults: {
					limit: <?= (int) $state->limit; ?>,
					offset: <?= (int) $state->offset; ?>,
					types: <?= json_encode($state->types); ?>
				}
			},
			tree: {
				theme: 'media://com_files/images/mootree.png'
			},
			types: <?= json_encode($state->types); ?>,
			container: <?= json_encode($container ? $container->slug : null); ?>,
			thumbnails: <?= json_encode($container ? $container->parameters->thumbnails : true); ?>
		};
	options = $extend(options, config);
	
	Files.app = new Files.App(options);

	//@TODO hide the uploader in a modal, make it pretty
	$('files-upload').setStyle('display', 'none').inject(document.body);
	$('files-show-uploader').addEvent('click', function(e){
		e.stop();

		var handleClose = function(){
			$('files-upload').setStyle('display', 'none').inject(document.body);
			SqueezeBox.removeEvent('close', handleClose);
		};
		SqueezeBox.addEvent('close', handleClose);
		SqueezeBox.open($('files-upload').setStyle('display', 'block'), {
			handler: 'adopt',
			size: {x: 700, y: $('files-upload').measure(function(){return this.getSize().y;})}
		});
	});

	$('files-new-folder-modal').getElement('form').addEvent('submit', function(e){
		e.stop();
		var element = $('files-new-folder-input');
		var value = element.get('value');
		if (value.length > 0) {
			var folder = new Files.Folder({name: value, folder: Files.app.getPath()});
			folder.add(function(response, responseText) {
				if (response.status === false) {
					return alert(response.error);
				}
				element.set('value', '');
				var el = response.item;
				var cls = Files[el.type.capitalize()];
				var row = new cls(el);
				Files.app.grid.insert(row);
				Files.app.tree.selected.insert({
					text: row.name,
					id: row.path,
					data: {
						path: row.path,
						url: '#'+row.path,
						type: 'folder'
					}
				});
				Files.app.tree.selected.toggle(false, true);

				SqueezeBox.close();
			});
		};
	});

    Files.createModal = function(container, button){
        var modal = $(container);
        document.body.grab(modal);
        modal.setStyle('display', 'none');
    	$(button).addEvent('click', function(e) {
    		e.stop();

    		var handleClose = function(){
				modal.setStyle('display', 'none').inject(document.body);
				SqueezeBox.removeEvent('close', handleClose);
			}, sizes = modal.measure(function(){return this.getSize();});
			SqueezeBox.addEvent('close', handleClose);
			SqueezeBox.open(modal.setStyle('display', 'block'), {
				handler: 'adopt',
				size: {x: sizes.x, y: sizes.y}
			});

			//@TODO fix this using onOpen event in SqueezeBox
    		var focus = modal.getElement('input.focus');
    		if (focus) {
        		focus.focus();
    		}
    	});
    };

    Files.createModal('files-new-folder-modal', 'files-new-folder-toolbar');

    var switchers = $$('.files-layout-switcher'),
    	slider = document.id('files-thumbs-size');
	
	if(slider.type != 'range') {
	    var container = slider.getParent('.files-layout-grid-resizer-container').addClass('fallback'),
		    newSlider = new Element('div', {
    		    'id': slider.id,
    			'class': 'slider'
    		}).grab(new Element('div', {'class': 'knob'}))
    		  .replaces(slider);
			
		// Create the new slider instance
	    new Slider(newSlider, newSlider.getElement('.knob'), {
	        range: [80, 200],
	        initialStep: slider.value,
	        onChange: function(value){
	        	Files.app.grid.setIconSize(value);
	        	Files.app.setDimensions.call(Files.app, true);
	        }
	    });
	    var slider = container;
	} else {
	    slider.addEvent('change', function(event){
	        Files.app.grid.setIconSize(this.value);
	        Files.app.setDimensions.call(Files.app, true);
	    });
	}
	
    switchers.filter(function(el) { 
        return el.get('data-layout') == Files.app.grid.layout;
    }).addClass('active');

    switchers.addEvent('click', function(e) {
    	e.stop();
    	var layout = this.get('data-layout');
    	Files.app.grid.setLayout(layout);
    	slider.setStyle('display', layout == 'icons' ? 'block' : 'none');
    	switchers.removeClass('active');
    	this.addClass('active');
    });
    if (Files.app.grid.layout != 'icons') {
    	slider.setStyle('display', 'none');
    }
});
</script>


<div id="files-app" class="-koowa-box -koowa-box-flex">
	<?= @template('templates_icons'); ?>
	<?= @template('templates_details'); ?>
	
	<div id="sidebar">
		<div id="files-tree"></div>
	</div>
	
	<div id="files-canvas" class="-koowa-box -koowa-box-vertical -koowa-box-flex">
	    <div class="path" style="height: 24px;">
	        <div class="files-toolbar-controls">
	        	<button id="files-show-uploader"><?= @text('Upload'); ?></button>
			    <button id="files-new-folder-toolbar"><?= @text('New Folder'); ?></button>
			    <button id="files-batch-delete"><?= @text('Delete'); ?></button>
			</div>
			<h3 id="files-title"></h3>
			<div class="files-layout-controls">
				<button class="files-layout-switcher" data-layout="icons">Icons</button>
				<button class="files-layout-switcher" data-layout="details">Details</button>
			</div>
		</div>
		<div class="view -koowa-box-scroll -koowa-box-flex">
			<div id="files-grid"></div>
		</div>
        <div class="files-layout-grid-resizer-container">
            <div class="files-layout-grid-resizer-wrap">
                <input id="files-thumbs-size" type="range" min="80" max="200" step="0.1" />
            </div>
        </div>
		<?= @helper('paginator.pagination') ?>
	
		<?= @template('uploader');?>
	</div>
	<div style="clear: both"></div>
</div>

<div>
	<div id="files-new-folder-modal" class="files-modal" style="display: none">
	<form>
		<input class="inputbox focus" type="text" id="files-new-folder-input" size="60" placeholder="<?= @text('Enter a folder name') ?>" />
		<button id="files-new-folder-create"><?= @text('Create'); ?></button>
	</form>
	</div>
</div>