<?php
/**
 * jUpgrade
 *
 * @version		$Id$
 * @package		MatWare
 * @subpackage	com_jupgradepro
 * @copyright	Copyright 2006 - 2011 Matias Aguire. All rights reserved.
 * @license		GNU General Public License version 2 or later.
 * @author		Matias Aguirre <maguirre@matware.com.ar>
 * @link		http://www.matware.com.ar
 */

// No direct access.
defined('_JEXEC') or die;

// Get the version
$version = "v{$this->version}";
// get params
$params	= $this->params;
// get document to add scripts
$document	= JFactory::getDocument();
$document->addScript('components/com_jupgradepro/js/dwProgressBar.js');
$document->addScript("components/com_jupgradepro/js/migrate.js");
$document->addScript('components/com_jupgradepro/js/requestmultiple.js');
$document->addStyleSheet("components/com_jupgradepro/css/jupgrade.css");
?>
<script type="text/javascript">

window.addEvent('domready', function() {

	/* Init jUpgrade */
	var jupgrade = new jUpgrade({
		method: '<?php echo $params->get("method") ? $params->get("method") : 0; ?>',
		skip_checks: <?php echo $params->get("skip_checks") ? $params->get("skip_checks") : 0; ?>,
    skip_templates: <?php echo $params->get("skip_templates") ? $params->get("skip_templates") : 0; ?>,
    skip_extensions: <?php echo $params->get("skip_extensions") ? $params->get("skip_extensions") : 0; ?>,
    positions: <?php echo $params->get("positions") ? $params->get("positions") : 0; ?>,
    debug: <?php echo $params->get("debug") ? $params->get("debug") : 0; ?>,
	});

});

</script>

<table width="100%">
	<tbody>
		<tr>
			<td width="100%" valign="top" align="center">

				<div id="error" class="error"></div>

				<div id="warning" class="warning">
					<?php echo JText::_('COM_JUPGRADEPRO_WARNING_SLOW'); ?>
				</div>

				<div id="update">
					<br /><img src="components/com_jupgradepro/images/update.png" align="middle" border="0"/><br />
					<h2><?php echo JText::_('START UPGRADE'); ?></h2><br />
				</div>

				<div id="checks">
					<p class="text"><?php echo JText::_('Checking and cleaning...'); ?></p>
					<div id="pb0"></div>
					<div><small><i><span id="checkstatus"><?php echo JText::_('Initialize...'); ?></span></i></small></div>
				</div>

				<div id="migration">
					<p class="text"><?php echo JText::_('Upgrading progress...'); ?></p>
					<div id="pb4"></div>
					<div><small><i><span id="status"><?php echo JText::_('Initialize...'); ?></span></i></small></div>
					<div id="counter">
						<i><small><b><span id="currItem">0</span></b> items /
						<b><span id="totalItems">0</span></b> items</small></i>
					</div>
				</div>

				<div id="done">
					<h2 class="done"><?php echo JText::_('Migration Successful!'); ?></h2>
				</div>
				<div id="info">
					<div id="info_version"><?php echo JText::_('jUpgradePro'); ?> <?php echo JText::_('Version').' <b>'.$this->version.'</b>'; ?></div>
					<div id="info_thanks">
						<p>
							<?php echo JText::_('Developed by'); ?> <i><a href="http://www.matware.com.ar/">Matware &#169;</a></i>  Copyleft 2006-2012<br />
							Licensed as <a href="http://www.gnu.org/licenses/old-licenses/gpl-2.0.html"><i>GNU General Public License v2</i></a><br />
						</p>
						<p>
							<a href="http://redcomponent.com/jupgrade">Project Site</a> /
							<a href="http://redcomponent.com/forum/92-jupgrade">Project Community</a> /
							<a href="http://redcomponent.com/forum/92-jupgrade/102880-jupgrade-faq">FAQ</a><br />
						</p>
					</div>
				</div>

				<div>
					<div id="debug"></div>
				</div>

			</td>
		</tr>
	</tbody>
</table>

<form action="index.php?option=com_jupgradepro" method="post" name="adminForm">
	<input type="hidden" name="option" value="com_jupgradepro" />
	<input type="hidden" name="task" value="" />
</form>
