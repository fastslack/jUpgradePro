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

<script src="<?php echo JUri::root(true); ?>/media/com_jupgradepro/js/jquery.terminal-1.11.4.js"></script>
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
		var url1 = 'index.php?option=com_jupgradepro&format=raw&task=ajax.show';
		var url2 = 'index.php?option=com_jupgradepro&format=raw&task=ajax.check';
		var url3 = 'index.php?option=com_jupgradepro&format=raw&task=ajax.step';
		var url4 = 'index.php?option=com_jupgradepro&format=raw&task=ajax.migrate';
		var url5 = 'index.php?option=com_jupgradepro&format=raw&task=ajax.cleanup';
		var url6 = 'index.php?option=com_jupgradepro&format=raw&task=ajax.checks';
		var url7 = 'index.php?option=com_jupgradepro&format=raw&task=ajax.cleantable';

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
							$.printConsole(term, '<?php echo JText::_('COM_JUPGRADEPRO_HELP_DESC'); ?>');
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
							var url1site = url1 + '&command=sites';

							$.get(url1site,	function(result) {

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
							var url2site = url2 + '&site=' + split[1];

							$.get(url2site,	function(result) {

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

							spinner = spinners['clock'];
							if (!spinner) {
									this.error('Spinner not found');
							} else {
									$.start(term, spinner, '<?php echo JText::_('COM_JUPGRADEPRO_CLEANUP_RUNNING'); ?>');
							}

							// Cleanup
							var url5site = url5 + '&site=' + split[1];

							$.get(url5site,	function(response5)
							{
								if (response5 !== undefined) {

									var object = jQuery.parseJSON( response5 );

									if (object.code >= 500)
									{
										$.printConsole(term, object.message);
										term.echo('<?php echo JText::_('COM_JUPGRADEPRO_HORIZONTAL_LIN3'); ?>');
										spinner = spinners['clock'];
										$.stop(term, spinner);
										return false;
									}

									$.stop(term, spinner, '<?php echo JText::_('COM_JUPGRADEPRO_CLEANUP_DONE'); ?>');

									setTimeout(function(){
									  $.printConsole(term, '<?php echo JText::_('COM_JUPGRADEPRO_MIGRATION_METHOD'); ?>', false, object.method);
										$.printConsole(term, '<?php echo JText::_('COM_JUPGRADEPRO_CURRENT_SITE_VER'); ?>', false, object.current_version);
										$.printConsole(term, '<?php echo JText::_('COM_JUPGRADEPRO_EXTERNAL_SITE_VER'); ?>', false, object.ext_version);

										$.start(term, spinner, '<?php echo JText::_('COM_JUPGRADEPRO_CHECKS_RUNNING'); ?>');

										// Checks
										url6 = url6 + '&site=' + split[1];

										$.get(url6,	function(response6) {
											if (response6 !== undefined) {

												var object = jQuery.parseJSON( response6 );

												// Set URL's
												url3 = url3 + '&site=' + split[1];
												url4 = url4 + '&site=' + split[1];

												// Declare promise
												var p = $.when(1);

												if (object.number >= 500)
												{
													$.printConsole(term, object.text);
													term.echo('<?php echo JText::_('COM_JUPGRADEPRO_HORIZONTAL_LIN3'); ?>');
													$.stop(term, spinner);
													return false;
												}

												if (object.code >= 500)
												{
													$.printConsole(term, object.message);
													term.echo('<?php echo JText::_('COM_JUPGRADEPRO_HORIZONTAL_LIN3'); ?>');
													$.stop(term, spinner);
													return false;
												}

												if (object.code == 409)
												{
													$.printConsole(term, object.message);
													$.stop(term, spinner);

													var history = term.history();
									        history.disable();
									        term.push(function(command) {
									            if (command.match(/^(Yes)$/i)) {

																	setTimeout(function(){
																		term.find('.cursor').hide();
																	  $.callStep(term, false, p, split[1], spinners, true);
																	}, 2000);
									                term.pop();
									                history.enable();

									            } else if (command.match(/^(No)$/i)) {

																	term.echo('<?php echo JText::_('COM_JUPGRADEPRO_HORIZONTAL_LIN3'); ?>');
									                term.pop();
									                history.enable();

									            }
									        }, {
									            prompt: '[[g;white;]|] (Yes/No) ? '
									        });
												}

												if (object.code == 200)
												{
													$.stop(term, spinner, object.message);

													setTimeout(function(){
													  $.callStep(term, false, p, split[1], spinners);
													}, 2000);

												}
											}

										});

									}, 2000);

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
			printConsole: function(term, message, resume, value) {

				if (typeof resume === 'undefined') {
				  resume = true;
				}

				var display = new String(message);

				display = display.replace(/{{NL}}/g, '\n');
				display = display.replace(/{{VARCHAR}}/g, value);
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
			start: function (term, spinner, message) {
				animation = true;
				i = 0;
				function set() {
					var spin = spinner.frames[i++ % spinner.frames.length];
						var text = '[[g;white;]|] ' + spin + ' ' + message;
						term.update(-1, text);
				};
				prompt = term.get_prompt();
				term.find('.cursor').hide();
				term.echo(' ');
				set();
				timer = setInterval(set, spinner.interval);
			}
		});

		$.extend({
			stop: function (term, spinner, message) {
				setTimeout(function() {
						clearInterval(timer);
						var frame = spinner.frames[i % spinner.frames.length];
						animation = false;
						term.find('.cursor').show();
						if (message)
						{
							term.update(-1, message);
						}
				}, 0);

				//term.update(-1, message);
			}
		});


		$.extend({
			callMigrate: function (term, data, p, sitename, spinners) {

				//$.start(term, spinners['dots2']);

				var ret = $.get(url4,	function(response) {

					var object = jQuery.parseJSON( response );

					if (object.number >= 500)
					{
						$.printConsole(term, object.text);
						term.echo('<?php echo JText::_('COM_JUPGRADEPRO_HORIZONTAL_LIN3'); ?>');
						return false;
					}

					if (object !== undefined) {

						var limit = parseInt(object.stop) - parseInt(object.start);

						var text = '•';
						for (i=0;i<=limit;i++)
						{
							text = text + '•';
						}

						promise = p.then(function(){

							setTimeout(function(){
								$.printConsole(term, '[[g;white;]|]  [[g;red;](][[i;yellow;]' + text + '][[g;red;])]', false);
								return $.callStep(term, data, p, sitename, spinners);
							}, 500);

						});
					}

				});

				return ret;
			}
		});


		$.extend({
			callStep: function (term, data, p, sitename, spinners, cleantable) {

				if (cleantable == true)
				{
					var url7site = url7 + '&site=' + sitename;

					$.get(url7site,	function(result) {
						if (result === undefined) {
							console.log('Clean table error.')
						}
					});
				}

				var ret = $.get(url3,	function(response) {

					var object = jQuery.parseJSON( response );

					if (object.name == undefined)
					{
						term.echo('<?php echo JText::_('COM_JUPGRADEPRO_HORIZONTAL_LIN2'); ?>');
						$.printConsole(term, '<?php echo JText::_('COM_JUPGRADEPRO_MIGRATION_FINISHED'); ?>');
						term.echo('<?php echo JText::_('COM_JUPGRADEPRO_HORIZONTAL_LIN3'); ?>');
						return false;
					}

					var stop = parseInt(object.cid) + parseInt(object.chunk);

					if (object.total < stop)
					{
						stop = object.total;
					}

					promise = p.then(function(){
						term.echo('<?php echo JText::_('COM_JUPGRADEPRO_HORIZONTAL_LIN2'); ?>');
						term.echo('[[g;white;]|]  [[b;green;] Migrating '+object.name+' (Start: '+object.cid+' - Stop: '+stop+' - Total: '+object.total+')');
					});

					if (object.end == true || object.code == 404)
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

							setTimeout(function(){
								return $.callMigrate(term, object, p, sitename, spinners);
							}, 500);

						});
					}else{

						promise = p.then(function(){
							setTimeout(function(){
								return $.callStep(term, data, p, sitename, spinners);
							}, 500);
						});
					}

				});

				return ret;
			}
		});

	});
</script>
