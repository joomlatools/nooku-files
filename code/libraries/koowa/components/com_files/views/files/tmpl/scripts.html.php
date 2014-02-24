<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-files for the canonical source repository
 */
defined('KOOWA') or die( 'Restricted access' );

/* DEBUG FILES:
<script src="media://koowa/com_files/js/spin.min.js" />

<script src="media://koowa/com_files/js/files.utilities.js" />
<script src="media://koowa/com_files/js/files.state.js" />
<script src="media://koowa/com_files/js/files.template.js" />
<script src="media://koowa/com_files/js/files.grid.js" />
<script src="media://koowa/com_files/js/files.tree.js" />
<script src="media://koowa/com_files/js/files.row.js" />
<script src="media://koowa/com_files/js/files.paginator.js" />
<script src="media://koowa/com_files/js/files.pathway.js" />

<script src="media://koowa/com_files/js/files.app.js" />
*/
JHtml::_('behavior.modal');

if (version_compare(JVERSION, '3.0', 'ge')) {
    JHtml::_('behavior.framework', true);
} else {
    JHtml::_('behavior.mootools', false);
}
?>

<?= @helper('behavior.koowa'); ?>
<?= @helper('behavior.local_dates'); ?>
<?= @helper('behavior.keepalive'); ?>
<?= @helper('behavior.tooltip'); ?>
<?= @helper('behavior.modal'); ?>
<?= @helper('bootstrap.load', array('wrapper' => false)); ?>
<?= @helper('behavior.tree'); ?>

<script src="media://koowa/com_files/js/history/history.js" />
<? if (JBrowser::getInstance()->getBrowser() === 'msie'): ?>
<script src="media://koowa/com_files/js/history/history.html4.js" />
<? endif; ?>

<script src="media://koowa/com_files/js/ejs/ejs.js" />

<script src="media://koowa/com_files/js/spin.min.js" />

<script src="media://koowa/com_files/js/files.utilities.js" />
<script src="media://koowa/com_files/js/files.state.js" />
<script src="media://koowa/com_files/js/files.template.js" />
<script src="media://koowa/com_files/js/files.grid.js" />
<script src="media://koowa/com_files/js/files.tree.js" />
<script src="media://koowa/com_files/js/files.row.js" />
<script src="media://koowa/com_files/js/files.paginator.js" />
<script src="media://koowa/com_files/js/files.pathway.js" />

<script src="media://koowa/com_files/js/files.app.js" />

<!--[if lte IE 9]>
<script data-inline src="media://koowa/com_files/js/jquery.placeholder.js" type="text/javascript"></script>
<script data-inline type="text/javascript">
kQuery(function($) {
    $('input, textarea').placeholder();
});
</script>
<![endif]-->

<?= @helper('icon.icon_map'); ?>