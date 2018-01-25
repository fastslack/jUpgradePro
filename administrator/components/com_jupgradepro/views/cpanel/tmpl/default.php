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

$user	= \JFactory::getUser();
$userId	= $user->get('id');
?>

<script src="/media/com_jupgradepro/js/jquery.terminal-1.11.0.js"></script>
<link href="/media/com_jupgradepro/css/jquery.terminal-1.11.0.min.css" rel="stylesheet"/>
<link href="/media/com_jupgradepro/css/jquery.terminal.custom.css" rel="stylesheet"/>

<section class="content">
	<div class="container">
		<div class="row">
				<div class="col-md-2"></div>
				<div class="col-md-8">

					<div id="jupgradeproconsole" class="terminal fill">
					</div>

				</div>
				<div class="col-md-2">&nbsp;</div>
		</div>
	</div>
</section>

<script type="text/javascript">
	jQuery(function($, undefined) {

		var url0 = '/media/com_jupgradepro/json/spinners.json';
		var url1 = 'index.php?option=com_jupgradepro&format=raw&task=ajax.show';
		var url2 = 'index.php?option=com_jupgradepro&format=raw&task=ajax.check';
		var url3 = 'index.php?option=com_jupgradepro&format=raw&task=ajax.step';
		var url4 = 'index.php?option=com_jupgradepro&format=raw&task=ajax.migrate';
		var url5 = 'index.php?option=com_jupgradepro&format=raw&task=ajax.cleanup';
		var url6 = 'index.php?option=com_jupgradepro&format=raw&task=ajax.checks';

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

						var split = command.split(' ');

						if (split[1] == 'show')
						{
							$.printConsole(term, '<?php echo JText::_('COM_JUPGRADEPRO_HELP_SHOW'); ?>');
						}

						if (split[1] == 'check')
						{
							$.printConsole(term, '<?php echo JText::_('COM_JUPGRADEPRO_HELP_CHECK'); ?>');
						}

						if (split[1] == 'migrate')
						{
							$.printConsole(term, '<?php echo JText::_('COM_JUPGRADEPRO_HELP_MIGRATE'); ?>');
						}

						if (!split[1])
						{
							$.printConsole(term, '<?php echo JText::_('                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    COM_JUPGRADEPRO_HELP_DESC'); ?>');
						}

					} else if (command.substring(0, 4) == 'show') {

						var split = command.split(' ');

						term.pause();

						if ((split[1] == 'config' && split.length <=2) || split[1] == 'undefined')
						{
							$.printConsole(term, '<?php echo JText::_('COM_JUPGRADEPRO_HELP_SHOW'); ?>');
						}
						else if (split[1] == 'config')
						{
							url1 = url1 + '&command=config&site=' + split[2];

							$.get(url1,	function(result) {

								if (result !== undefined) {
									$.printConsole(term, result);
								}
							});
						}
						else if (split[1] == 'sites')
						{
							url1 = url1 + '&command=sites';

							$.get(url1,	function(result) {

								if (result !== undefined) {
									$.printConsole(term, result);
								}
							});
						}else{
							$.printConsole(term, '<?php echo JText::_('COM_JUPGRADEPRO_HELP_SHOW'); ?>');
						}

					} else if (command.substring(0, 5) == 'check') {

						var split = command.split(' ');

						term.pause();

						if (split.length <=1 || split[1] == 'undefined')
						{
							 $.printConsole(term, '<?php echo JText::_('COM_JUPGRADEPRO_HELP_CHECK'); ?>');
						}
						else if (split[1] != 'undefined')
						{
							url2 = url2 + '&site=' + split[1];

							$.get(url2,	function(result) {

								if (result !== undefined) {
									$.printConsole(term, result);
								}
							});

						}else{
							$.printConsole(term, '<?php echo JText::_('COM_JUPGRADEPRO_COMMAND_'); ?>');
						}

					} else if (command.substring(0, 7) == 'migrate') {

						var split = command.split(' ');

						term.pause();

						if (split.length <=1 || split[1] == 'undefined')
						{
							 $.printConsole(term, '<?php echo JText::_('COM_JUPGRADEPRO_HELP_MIGRATE'); ?>');
						}
						else if (split[1] != 'undefined')
						{
							term.echo('<?php echo JText::_('COM_JUPGRADEPRO_HORIZONTAL_LINE'); ?>');

							// Cleanup
							url5 = url5 + '&site=' + split[1];

							$.get(url5,	function(result) {
								if (result !== undefined) {

									$.printConsole(term, result, false);

									term.echo('<?php echo JText::_('COM_JUPGRADEPRO_HORIZONTAL_LIN2'); ?>');

									// Checks
									url6 = url6 + '&site=' + split[1];
		console.log(url6);
									$.get(url6,	function(response) {
										if (response !== undefined) {

											var object = jQuery.parseJSON( response );

		console.log(object);

											if (object.number >= 500)
											{
												$.printConsole(term, object.text);
												term.echo('<?php echo JText::_('COM_JUPGRADEPRO_HORIZONTAL_LIN3'); ?>');
												return false;
											}

											if (object.code >= 500)
											{
												$.printConsole(term, object.message);
												term.echo('<?php echo JText::_('COM_JUPGRADEPRO_HORIZONTAL_LIN3'); ?>');
												return false;
											}

											if (object.code == 200)
											{
												$.printConsole(term, object.message);
												term.echo('<?php echo JText::_('COM_JUPGRADEPRO_HORIZONTAL_LIN2'); ?>');

												// Set URL's
												url3 = url3 + '&site=' + split[1];
												url4 = url4 + '&site=' + split[1];

												// Declare promise
												var p = $.when(1);

												callStep(term, false, p, split[1], spinners);
											}
										}

									});

								}
							});

						}else{
							$.printConsole(term, '<?php echo JText::_('COM_JUPGRADEPRO_COMMAND_'); ?>');
						}

					} else {
						$.printConsole(term, '<?php echo JText::_('COM_JUPGRADEPRO_COMMAND_NOT_FOUND'); ?>');
					}
			}, {
				greetings: '[[b;red;]       _ __  __                           __     ____           \n      (_) / / /___  ____ __________ _____/ /__  / __ \\_________ \n     / / / / / __ \\/ __ `/ ___/ __ `/ __  / _ \\/ /_/ / ___/ __ \\ \n    / / /_/ / /_/ / /_/ / /  / /_/ / /_/ /  __/ ____/ /  / /_/ /\n __/ /\\____/ .___/\\__, /_/   \\__,_/\\__,_/\\___/_/   /_/   \\____/ \n/___/     /_/    /____/                                  ][[big;orange;]v3.8]     \n\n\n\n  Type [[b;green;]help [command\\]] for assistance\n\n  Commands: [[ib;yellow;] show, check, migrate]  \n\n',
				name: 'jupgradeproconsole',
				height: 600,
				prompt: '# '
			});

		});


		/*
		 * Load the saved planning data to jQuery form
		 */
		$.extend({
			printConsole: function(term, message, resume) {

				if (typeof resume === 'undefined') {
				  resume = true;
				}

				var display = new String(message);
				display = display.replace(/{{NL}}/g, '\n');
				term.echo(display);

				if (resume == true)
				{
					term.resume();
				}
			}
		});

		/*
		 * Load the saved planning data to jQuery form
		 */
		$.extend({
			start: function (term, spinner) {
				animation = true;
				i = 0;
				function set() {
						var text = spinner.frames[i++ % spinner.frames.length];
						term.set_prompt(text);
				};
				prompt = term.get_prompt();
				term.find('.cursor').hide();
				set();
				timer = setInterval(set, spinner.interval);
			}
		});

		$.extend({
			stop: function (term, spinner) {
					setTimeout(function() {
							clearInterval(timer);
							var frame = spinner.frames[i % spinner.frames.length];
							term.set_prompt(prompt).echo(frame);
							animation = false;
							term.find('.cursor').show();
					}, 0);
			}
		});

		function callMigrate(term, data, p, sitename, spinners) {

			//url4 = url4 + '&site=' + sitename;
			console.log('FIRE!: '+ url4);

			var ret = $.get(url4,	function(response) {

console.log('RESPONSE: ' + response);

				var object = jQuery.parseJSON( response );

console.log(object);

				if (object.number >= 500)
				{
					$.printConsole(term, object.text);
					term.echo('<?php echo JText::_('COM_JUPGRADEPRO_HORIZONTAL_LIN3'); ?>');
					return false;
				}

				if (object !== undefined) {

					var text = '';
					for (i=0;i<=object.chunk;i++)
					{
						text = text + '•';
					}

					$.printConsole(term, '[[g;white;]|]  [[g;red;](][[i;yellow;]' + text + '][[g;red;])]', false);
				}

				promise = p.then(function(){
					//$.start(term, spinners['dots2']);
					return callStep(term, data, p, sitename, spinners);
				});

			});

			return ret;
		}

		function callStep(term, data, p, sitename, spinners) {

			console.log('FIRE!: '+ url3);

			var ret = $.get(url3,	function(response) {

console.log('RESPONSE: ' + response);

				var object = jQuery.parseJSON( response );
				var stop = parseInt(object.cid) + parseInt(object.chunk);

				if (object.total < stop)
				{
					stop = object.total;
				}

				term.echo('<?php echo JText::_('COM_JUPGRADEPRO_HORIZONTAL_LIN2'); ?>');
				term.echo('[[g;white;]|]  [[b;green;] Migrating '+object.name+' (Start: '+object.cid+' - Stop: '+stop+' - Total: '+object.total+')');

				if (object.end == true)
				{
					promise = p.then(function(){
						$.printConsole(term, '<?php echo JText::_('COM_JUPGRADEPRO_HORIZONTAL_LIN2'); ?>');
						$.printConsole(term, '<?php echo JText::_('COM_JUPGRADEPRO_FINISH_STEP'); ?>');
						$.printConsole(term, '<?php echo JText::_('COM_JUPGRADEPRO_HORIZONTAL_LIN3'); ?>');
					});
					return false;
				}

				if (object.stop != -1)
				{
					promise = p.then(function(){
						return callMigrate(term, object, p, sitename, spinners);
					});
				}else{
					promise = p.then(function(){
						return callStep(term, data, p, sitename, spinners);
					});
				}

			});

			return ret;
		}

	});
</script>
