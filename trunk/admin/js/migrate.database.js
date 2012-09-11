/**
 * jUpgrade
 *
 * @version			$Id$
 * @package			MatWare
 * @subpackage	com_jupgradepro
 * @author      Matias Aguirre <maguirre@matware.com.ar>
 * @link        http://www.matware.com.ar
 * @license			GNU General Public License version 2 or later; see LICENSE.txt
 */

var jUpgrade = new Class({

  Implements: [Options, Events],

  options: {
    mode: 1,
    skip_checks: 0,
    skip_templates: 0,
    skip_extensions: 0,
    positions: 0,
    debug: 0
  },

	initialize: function(options) {
		var self = this;

		this.setOptions(options);

		$('warning').setStyle('display', 'none');
		$('error').setStyle('display', 'none');
		$('checks').setStyle('display', 'none');
		$('migration').setStyle('display', 'none');
		$('templates').setStyle('display', 'none');
		$('files').setStyle('display', 'none');
		$('extensions').setStyle('display', 'none');
		$('done').setStyle('display', 'none');

		$('update').addEvent('click', function(e) {
				self.checks(e);
		});

	},

	/**
	 * Fix needed!! Internal function to get jUpgrade settings
	 *
	 * @return	bool
	 * @since	1.2.0
	 */
	updateSettings: function(e) {
		var request = new Request({
			url: 'index.php?option=com_jupgradepro&format=raw&controller=ajax&task=getParams',
			method: 'get',
			noCache: true,
			onComplete: function(response) {
				var object = JSON.decode(response);
				this.options.directory = object.directory;
			}
		}).send();
	},

	/**
	 * Run the checks
	 *
	 * @return	bool
	 * @since	1.2.0
	 */
	checks: function(e) {
		var self = this;

		// this/e.target is current target element.
		if (e.stopPropagation) {
			e.stopPropagation(); // Stops some browsers from redirecting.
		}

		var mySlideUpdate = new Fx.Slide('update');
		mySlideUpdate.toggle();

		// Check skip from settings
		if (self.options.skip_checks != 1) {

			var mySlideChecks = new Fx.Slide('checks');
			mySlideChecks.hide();
			$('checks').setStyle('display', 'block');
			mySlideChecks.toggle();

			var pb0 = new dwProgressBar({
				container: $('pb0'),
				startPercentage: 33,
				speed: 1000,
				boxID: 'pb0-box',
				percentageID: 'pb0-perc',
				displayID: 'text',
				displayText: false
			});

			text = document.getElementById('checkstatus');
			text.innerHTML = 'Checking and cleaning...';

			//
			// Cleanup call
			//
			var cleanup = new Request({
				url: 'index.php?option=com_jupgradepro&format=raw&view=ajax&task=cleanup',
				method: 'get',
				noCache: true
			}); // end Request		

			cleanup.addEvents({
				'complete': function(response) {

					var object = JSON.decode(response);

					if (self.options.debug_php == 1) {
						text = document.getElementById('debug');
						text.innerHTML = text.innerHTML + '<br><br>==========<br><b>[cleanup]</b><br><br>' +object.text;
					}

					if (object.number == 100) {
						pb0.set(100);
						pb0.finish();
						self.migrate(e);
					}

				}
			});

			//
			// Checks
			//
			var checks = new Request({
				url: 'index.php?option=com_jupgradepro&format=raw&view=ajax&task=checks',
				method: 'get',
				noCache: true
			}); // end Request		

			checks.addEvents({
				'complete': function(response) {

					pb0.set(66);

					var object = JSON.decode(response);

					if (self.options.debug_php == 1) {
						text = document.getElementById('debug');
						text.innerHTML = text.innerHTML + '<br><br>==========<br><b>[checks]</b><br><br>' +object.text;
					}

					if (object.number == 100) {
						cleanup.send();
					}

				}
			});

			// Start the checks
			checks.send();

		}else{
			self.download(e);
		}

	}, // end function

	/**
	 * Run the migration
	 *
	 * @return	bool
	 * @since	1.2.0
	 */
	migrate: function(e) {
		var self = this;

		var request = new Request({
			url: 'index.php?option=com_jupgradepro&format=raw&view=ajax&task=migrate',
			method: 'get',
			noCache: true,
			//data: 'directory=' + self.options.directory,
			onComplete: function(response) {

				var object = JSON.decode(response);

				pb4.set(object.step*11);
				text = document.getElementById('status');
				text.innerHTML = 'Migrating ' + object.name;

				if (self.options.debug_php == 1) {
					text = document.getElementById('debug');
					text.innerHTML = text.innerHTML + '<br><br>==========<br><b>['+object.step+'] ['+object.name+']</b><br><br>' +object.text;
				}

				if (object.step >= 10 || object.step == '') {
					pb4.finish();

					// Shutdown periodical
					$clear(migration_periodical);

					// Run templates step
					if (self.options.skip_templates == 1) {
						if (self.options.skip_files == 1) {
							self.done();
						}else{
							self.files();
						}
					}else{
						self.templates();
					}
				}
			}
		});

		var runMigration = function() {
			request.send();
		};

		var mySlideMigrate = new Fx.Slide('migration');
		mySlideMigrate.hide();
		$('migration').setStyle('display', 'block');
		mySlideMigrate.toggle();

		pb4 = new dwProgressBar({
			container: $('pb4'),
			startPercentage: 5,
			speed: 1000,
			boxID: 'pb4-box',
			percentageID: 'pb4-perc',
			displayID: 'text',
			displayText: false
		});

		var myScroll = new Fx.Scroll(window).toBottom();

		migration_periodical = runMigration.periodical(1500);

	}, // end function

	/**
	 * Run the templates
	 *
	 * @return	bool
	 * @since	1.2.0
	 */
	templates: function(e) {
		var self = this;

		var mySlideTem = new Fx.Slide('templates');
		mySlideTem.hide();
		$('templates').setStyle('display', 'block');
		mySlideTem.toggle();

		var pb5 = new dwProgressBar({
			container: $('pb5'),
			startPercentage: 10,
			speed: 1000,
			boxID: 'pb5-box',
			percentageID: 'pb5-perc',
			displayID: 'text',
			displayText: false
		});

		var myScroll = new Fx.Scroll(window).toBottom();


		//
		// Templates Files
		//
		var templates_files = new Request({
			url: 'index.php?option=com_jupgradepro&format=raw&view=ajax&task=templatesfiles',
			method: 'get',
			noCache: true
		}); // end Request		

		templates_files.addEvents({
			'complete': function(response) {

				pb5.set(100);
				pb5.finish();

				var object = JSON.decode(response);

				if (self.options.debug_php == 1) {
					text = document.getElementById('debug');
					text.innerHTML = text.innerHTML + '<br><br>==========<br><b>[templates_files]</b><br><br>' +object.text;
				}

				if (object.number == 100) {
					self.extensions();
					//self.done();
				}

			}
		});

		//
		// Templates 
		//
		var templates = new Request({
			url: 'index.php?option=com_jupgradepro&format=raw&view=ajax&task=templates',
			method: 'get',
			noCache: true
		}); // end Request		

		templates.addEvents({
			'complete': function(response) {

				pb5.set(50);

				var object = JSON.decode(response);

				if (self.options.debug_php == 1) {
					text = document.getElementById('debug');
					text.innerHTML = text.innerHTML + '<br><br>==========<br><b>[templates_db]</b><br><br>' +object.text;
				}

				if (object.number == 100) {
					templates_files.send();
				}

			}
		});

		// Start the checks
		templates.send();

	}, // end function

	/**
	 * Run the files copying
	 *
	 * @return	bool
	 * @since	1.2.0
	 */
	files: function(e) {
		var self = this;

		var mySlideTem = new Fx.Slide('files');
		mySlideTem.hide();
		$('files').setStyle('display', 'block');
		mySlideTem.toggle();

		var pb6 = new dwProgressBar({
			container: $('pb6'),
			startPercentage: 20,
			speed: 1000,
			boxID: 'pb6-box',
			percentageID: 'pb6-perc',
			displayID: 'text',
			displayText: false
		});

		var myScroll = new Fx.Scroll(window).toBottom();

		//
		// Files 
		//
		var files = new Request({
			url: 'index.php?option=com_jupgradepro&format=raw&view=ajax&task=files',
			method: 'get',
			noCache: true
		}); // end Request		

		files.addEvents({
			'complete': function(response) {

				pb6.set(100);
				pb6.finish();

				var object = JSON.decode(response);

				if (self.options.debug_php == 1) {
					text = document.getElementById('debug');
					text.innerHTML = text.innerHTML + '<br><br>==========<br><b>[files]</b><br><br>' +object.text;
				}

				if (self.options.skip_extensions == 1) {
					self.done();
				}else{
					self.extensions();
				}

			}
		});

		// Start the checks
		files.send();

	}, // end function

	/**
	 * Run the extensions
	 *
	 * @return	bool
	 * @since	1.2.0
	 */
	extensions: function(e) {
		var self = this;

		var ext_request = new Request({
			url: 'index.php?option=com_jupgradepro&format=raw&view=ajax&task=extensions',
			method: 'get',
			noCache: true,
			data: 'directory=' + self.options.directory,
			onComplete: function(response) {

				//alert(response);

				var object = JSON.decode(response);

				if (self.options.debug_php == 1) {
					text = document.getElementById('debug');
					text.innerHTML = text.innerHTML + '<br><br>==========<br><b>['+object.step+'] ['+object.name+']</b><br><br>'+object.text;
				}

				pb7.set(100);
				text = document.getElementById('status_ext');
				text.innerHTML = 'Migrating ' + object.name;

				if (object.step == object.lastid) {
					pb7.finish();

					// Shutdown periodical
					$clear(extension_periodical);

					// Run templates step
					self.done();
				}
			}

		});

		var runExtensionsMigration = function() {
			ext_request.send();
		};

		var mySlideExt = new Fx.Slide('extensions');
		mySlideExt.hide();
		$('extensions').setStyle('display', 'block');
		mySlideExt.toggle();

		pb7 = new dwProgressBar({
			container: $('pb7'),
			startPercentage: 50,
			speed: 1000,
			boxID: 'pb7-box',
			percentageID: 'pb7-perc',
			displayID: 'text',
			displayText: false
		});

		var myScroll = new Fx.Scroll(window).toBottom();

		extension_periodical = runExtensionsMigration.periodical(2000);

	}, // end function

	/**
	 * Run the done
	 *
	 * @return	bool
	 * @since	1.2.0
	 */
	done: function(e) {
		var self = this;

		var myScroll = new Fx.Scroll(window).toBottom();

		var mySlideDone = new Fx.Slide('done');
		mySlideDone.hide();
		$('done').setStyle('display', 'block');
		mySlideDone.toggle();

	} // end function
});
