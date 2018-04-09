<?php
/**
 * jUpgradePro
 *
 * @version $Id:
 * @package jUpgradePro
 * @copyright Copyright (C) 2004 - 2018 Matware. All rights reserved.
 * @author Matias Aguirre
 * @email maguirre@matware.com.ar
 * @link http://www.matware.com.ar/
 * @license GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

JText::script('COM_JUPGRADEPRO_HELP_SHOW');
JText::script('COM_JUPGRADEPRO_HELP_CHECK');
JText::script('COM_JUPGRADEPRO_HELP_MIGRATE');
JText::script('COM_JUPGRADEPRO_HELP_DESC');
JText::script('COM_JUPGRADEPRO_COMMAND_');
JText::script('COM_JUPGRADEPRO_COMMAND_NOT_FOUND');
JText::script('COM_JUPGRADEPRO_CHECKS_RUNNING');
JText::script('COM_JUPGRADEPRO_CLEANUP_RUNNING');
JText::script('COM_JUPGRADEPRO_CLEANUP_DONE');
JText::script('COM_JUPGRADEPRO_MIGRATION_METHOD');
JText::script('COM_JUPGRADEPRO_CURRENT_SITE_VER');
JText::script('COM_JUPGRADEPRO_EXTERNAL_SITE_VER');
JText::script('COM_JUPGRADEPRO_HORIZONTAL_LINE');
JText::script('COM_JUPGRADEPRO_HORIZONTAL_LIN2');
JText::script('COM_JUPGRADEPRO_HORIZONTAL_LIN3');
JText::script('COM_JUPGRADEPRO_FINISH_STEP');
JText::script('COM_JUPGRADEPRO_MIGRATION_FINISHED');

$user	= \JFactory::getUser();
$userId	= $user->get('id');
?>

<script src="<?php echo JUri::root(true); ?>/media/com_jupgradepro/js/jquery.terminal-1.11.4.js"></script>
<script src="<?php echo JUri::root(true); ?>/media/com_jupgradepro/js/jupgradepro.js"></script>

<link href="<?php echo JUri::root(true); ?>/media/com_jupgradepro/css/jquery.terminal-1.11.4.min.css" rel="stylesheet"/>
<link href="<?php echo JUri::root(true); ?>/media/com_jupgradepro/css/jquery.terminal.custom.css" rel="stylesheet"/>

<section class="content">
	<div class="container">
		<div class="row">
				<div class="col-md-1"></div>
				<div class="col-md-10">

					<div id="jupgradeproconsole" class="terminal fill">
					</div>

				</div>
				<div class="col-md-1">&nbsp;</div>
		</div>
	</div>
</section>

<script type="text/javascript">
	jQuery(function($, undefined) {

		var url0 = '<?php echo JUri::root(true); ?>/media/com_jupgradepro/json/spinners.json';

	  $.getJSON(url0, function(spinners) {
	    var animation = false;
	    var timer;
	    var prompt;
	    var spinner;
	    var i;

	    // Initialize terminal
	    $('#jupgradeproconsole').terminal(function(command, term) {

	        if (command.substring(0, 4) == 'help')
	        {

	          $.printHelp(term, command);

	        } else if (command.substring(0, 4) == 'show') {

	          $.printShow(term, command);

	        } else if (command.substring(0, 5) == 'check') {

	          $.checkSite(term, command);

	        } else if (command.substring(0, 7) == 'migrate') {

	          $.migrateSite(term, command, spinners);

	        } else {

	          $.printConsole(term, Joomla.JText._("COM_JUPGRADEPRO_COMMAND_NOT_FOUND"));

	        }

	    }, {
	      greetings: '[[b;red;]       _ __  __                           __     ____           \n      (_) / / /___  ____ __________ _____/ /__  / __ \\_________ \n     / / / / / __ \\/ __ `/ ___/ __ `/ __  / _ \\/ /_/ / ___/ __ \\ \n    / / /_/ / /_/ / /_/ / /  / /_/ / /_/ /  __/ ____/ /  / /_/ /\n __/ /\\____/ .___/\\__, /_/   \\__,_/\\__,_/\\___/_/   /_/   \\____/ \n/___/     /_/    /____/                                  ][[big;orange;]v3.8]     \n\n\n\n  Type [[b;green;]help [command\\]] for assistance\n\n  Commands: [[ib;yellow;] show, check, migrate]  \n\n',
	      name: 'jupgradeproconsole',
	      height: 600,
	      prompt: '# '
	    });

	  });

	});
</script>
