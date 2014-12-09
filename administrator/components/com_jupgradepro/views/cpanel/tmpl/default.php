<?php
/**
* jUpgradePro
*
* @version $Id:
* @package jUpgradePro
* @copyright Copyright (C) 2004 - 2013 Matware. All rights reserved.
* @author Matias Aguirre
* @email maguirre@matware.com.ar
* @link http://www.matware.com.ar/
* @license GNU General Public License version 2 or later; see LICENSE
*/
// No direct access.
defined('_JEXEC') or die;

// Get the version
$version = "v{$this->version}";
// get params
$params	= $this->params;
// get document to add scripts
$document	= JFactory::getDocument();
$document->addScript('../media/com_jupgradepro/js/RadialProgressBar.js');
$document->addScript("../media/com_jupgradepro/js/migrate.js");
$document->addScript('../media/com_jupgradepro/js/requestmultiple.js');
$document->addStyleSheet("../media/com_jupgradepro/css/jupgradepro.css");
?>

<link href='http://fonts.googleapis.com/css?family=Chivo:400,400italic' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Gudea:400,400italic' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Ubuntu' rel='stylesheet' type='text/css'>

<script type="text/javascript">

window.addEvent('domready', function() {

	/* Init jUpgradePro */
	var jUpgradePro = new jUpgradepro({
		method: '<?php echo $params->method ? $params->method : 0; ?>',
		positions: <?php echo $params->positions ? $params->positions : 0; ?>,
		skip_checks: <?php echo $params->skip_checks ? $params->skip_checks : 0; ?>,
		debug_step: <?php echo $params->debug_step ? $params->debug_step : 0; ?>,
		debug_migrate: <?php echo $params->debug_migrate ? $params->debug_migrate : 0; ?>,
		debug_check: <?php echo $params->debug_check ? $params->debug_check : 0; ?>,
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
					<br /><img src="../media/com_jupgradepro/images/update.png" align="middle" border="0"/><br />
					<h2><?php echo JText::_('START'); ?></h2><br />
				</div>

				<div id="checks">
					<p class="text"><?php echo JText::_('Checking and cleaning...'); ?></p>
					<div id="pb0" data-progress="1%"></div>
					<div><small><i><span id="checkstatus"><?php echo JText::_('Initialize...'); ?></span></i></small></div>
				</div>

				<div id="migration">
					<p class="text" id="migrate_text"><?php echo JText::_('Upgrading progress...'); ?></p>
					<div id="pb4" data-progress="1%"></div>
					<div id="counter">
						<i><small><b><span id="currItem">0</span></b> items /
						<b><span id="totalItems">0</span></b> items</small></i>
					</div>
				</div>

				<div id="files">
					<p class="text"><?php echo JText::_('Copying images/media files...'); ?></p>
					<div id="pb5" data-progress="1%"></div>
					<div><small><i><span id="files_status"><?php echo JText::_('Initialize...'); ?></span></i></small></div>
					<div id="files_counter">
						<i><small><b><span id="files_currItem">0</span></b> items /
						<b><span id="files_totalItems">0</span></b> items</small></i>
					</div>
				</div>

				<div id="templates">
					<p class="text"><?php echo JText::_('Copying templates...'); ?></p>
					<div id="pb6" data-progress="1%"></div>
				</div>

				<div id="extensions">
					<p class="text" id="ext_text"><?php echo JText::_('Upgrading 3rd extensions...'); ?></p>
					<div id="pb7" data-progress="1%"></div>
					<div id="ext_counter">
						<i><small><b><span id="ext_currItem">0</span></b> items /
						<b><span id="ext_totalItems">0</span></b> items</small></i>
					</div>
				</div>

				<div id="done">
					<h2 class="done"><?php echo JText::_('Migration Successful!'); ?></h2>
				</div>

				<div id="info">
					<div id="info_version"><i><?php echo JText::_('jUpgradePro'); ?></i> <?php echo JText::_('Version').' <b>'.$this->version.'</b>'; ?></div>
					<div id="info_thanks">
						<p>
							<?php echo JText::_('Developed by'); ?> <i><a href="http://www.matware.com.ar/">Matware &#169;</a></i>  Copyleft 2006-2014<br />
							Licensed as <a href="http://www.gnu.org/licenses/old-licenses/gpl-2.0.html"><i>GNU General Public License v2</i></a><br />
						</p>
						<h3>
							<a href="http://www.matware.com.ar/proyects/jupgradepro.html">Project Site</a> /
							<a href="http://www.matware.com.ar/forums/jupgradepro.html">Community</a> /
							<a href="https://github.com/fastslack/jUpgradePro">GitHub</a> /
							<a href="http://matware.com.ar/documentation/jupgradepro/main-table-of-contents.html">Documentation</a><br />
						</h3>
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
