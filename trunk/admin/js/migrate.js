/**
 * jUpgradePro
 *
 * @version		    $Id$
 * @package		    MatWare
 * @subpackage    com_jupgradepro
 * @author        Matias Aguirre <maguirre@matware.com.ar>
 * @link          http://www.matware.com.ar
 * @copyright		  Copyright 2004 - 2013 Matias Aguirre. All rights reserved.
 * @license		    GNU General Public License version 2 or later; see LICENSE.txt
 */
var jUpgradepro = new Class({

  Implements: [Options, Events],

  options: {
    method: 'rest',
    skip_checks: 0,
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
		$('files').setStyle('display', 'none');
		$('templates').setStyle('display', 'none');
		$('extensions').setStyle('display', 'none');
		$('done').setStyle('display', 'none');

		$('update').addEvent('click', function(e) {
				self.checks(e);
		});

	},

	/**
	 * Fix needed!! Internal function to get jUpgradePro settings
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
				url: 'index.php?option=com_jupgradepro&format=raw&task=ajax.cleanup',
				method: 'get',
				noCache: true
			}); // end Request		

			cleanup.addEvents({
				'complete': function(response) {

					var object = JSON.decode(response);

					if (self.options.debug == 1) {
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
				url: 'index.php?option=com_jupgradepro&format=raw&task=ajax.checks',
				method: 'get',
				noCache: true
			}); // end Request		

			checks.addEvents({
				'complete': function(response) {
					console.log(response);
					pb0.set(66);

					var object = JSON.decode(response);

					if (self.options.debug == 1) {
						text = document.getElementById('debug');
						text.innerHTML = text.innerHTML + '<br><br>==========<br><b>[checks]</b><br><br>' +response;
						console.log(response);
					}

					if (object.number > 400) {
						$('error').setStyle('display', 'block');
						text = document.getElementById('error');
						text.innerHTML = object.text;
					}

					if (object.number == 100) {
						cleanup.send();
					}

				}
			});

			// Start the checks
			checks.send();

		}else{
			self.migrate(e);
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

		var method = self.options.method;

		if (method == 'database') {
			method = 'ajax';
		}

		// CSS stuff
		$('migration').setStyle('display', 'block');
		$('warning').setStyle('display', 'block');

		var mySlideWarning = new Fx.Slide('warning');

		setTimeout(function() {
			mySlideWarning.slideOut();
		}, 10000); 

		// Progress bar
		pb4 = new dwProgressBar({
			container: $('pb4'),
			startPercentage: 5,
			speed: 1000,
			boxID: 'pb4-box',
			percentageID: 'pb4-perc',
			displayID: 'text',
			displayText: false
		});

		// Get the status element
		migrate_status = document.getElementById('migrate_status');
		// Get the currItem element
		currItem = document.getElementById('currItem');
		// Get the totalItems element
		totalItems = document.getElementById('totalItems');

		// Declare counter
		var counter = 0;

		//
		// 
		//
		var row = new Request({
			link: 'chain',
			method: 'get'
		}); // end Request

		// Adding event to the row request
		row.addEvents({
			'complete': function(row_response) {

				var row_object = JSON.decode(row_response);

				currItem.innerHTML = row_object.cid;

				if (self.options.debug == 1) {
					text.innerHTML = text.innerHTML + '<br><br>==========<br><b>[ROW: '+row_object.name+']</b><br><br>' +row_response;
				}

				if (row_object.cid == row_object.stop.toInt()+1 || row_object.next == 1 ) {
					if (row_object.end == 1) {
						pb4.finish();
						this.cancel();
						step.cancel();
						self.extensions();
					} else if (row_object.next == 1) {
						step.send();
					}
				}
			}
		});

		var rm = new Request.Multiple({
			onRequest : function() {},
			onComplete : function() {}
		});

		//
		// 
		//
		var step = new Request({
			link: 'chain',
			url: 'index.php?option=com_jupgradepro&format=raw&task=ajax.step',
			method: 'get'
		}); // end Request		

		text = document.getElementById('debug');

		step.addEvents({
			'complete': function(response) {

				var object = JSON.decode(response);

				// Redirect if total == 0
				if (object.total == 0) {
					if (object.end == 1) {
						pb4.finish();
						this.cancel();
						self.extensions();
					}else{
						step.send();
					}
				}

				if (self.options.debug == 1) {
					text.innerHTML = text.innerHTML + '<br><br>==========<br><b>[STEP '+object.name+']</b><br><br>' +response;
				}

				// Changing title and statusbar
				pb4.set(object.id*6);

				migrate_status.innerHTML = 'Migrating ' + object.title;
				if (object.middle != true) {
					currItem.innerHTML = object.cid;
				}
				totalItems.innerHTML = object.total;

				// Start the checks
				row.options.url = 'index.php?option=com_jupgradepro&format=raw&task=ajax.migrate&table='+object.name;	

				// Running the request[s]
				if (method == 'ajax') {
					if (object.total != 0) {
						row.send();
					}
				} else if (method == 'rest') {
					for (i=object.start;i<=object.stop;i++) {
						var reqname = object.name+i;
						rm.addRequest(reqname, row);
					}
					rm.runAll();
				}
			}
		});

		step.send();

		// Scroll the window
		//var myScroll = new Fx.Scroll(window).toBottom();

	}, // end function

	/**
	 * Run the migration
	 *
	 * @return	bool
	 * @since	1.2.0
	 */
	extensions: function(e) {
		var self = this;

		var method = self.options.method;

		if (method == 'database') {
			method = 'ajax';
		}

		// Progress bar
		pb7 = new dwProgressBar({
			container: $('pb7'),
			startPercentage: 5,
			speed: 1000,
			boxID: 'pb7-box',
			percentageID: 'pb7-perc',
			displayID: 'text',
			displayText: false
		});

		// Get the status element
		ext_status = document.getElementById('ext_status');
		// Get the currItem element
		ext_currItem = document.getElementById('ext_currItem');
		// Get the totalItems element
		ext_totalItems = document.getElementById('ext_totalItems');
		// Get the debug
		ext_text = document.getElementById('debug');

		// Declare counter
		var counter = 0;

		//
		// Declare the row request
		//
		var ext_row = new Request({
			link: 'chain',
			method: 'get'
		}); // end Request

		// Adding event to the row request
		ext_row.addEvents({
			'complete': function(row_response) {

				var row_object = JSON.decode(row_response);

				ext_currItem.innerHTML = row_object.cid;

				if (self.options.debug == 1) {
					ext_text.innerHTML = ext_text.innerHTML + '<br><br>==========<br><b>[ROW: '+row_object.name+']</b><br><br>' +row_response;
				}

				if (row_object.cid == row_object.stop.toInt()+1 || row_object.next == 1 ) {
					if (row_object.end == 1) {
						pb7.set(100);
						pb7.finish();
						this.cancel();
						ext_step.cancel();
						self.done();
					} else if (row_object.next == 1) {
						ext_step.send();
					}
				}
			}
		});

		var ext_rm = new Request.Multiple({
			onRequest : function() {},
			onComplete : function() {}
		});

		//
		// Declare the step event
		//
		var ext_step = new Request({
			link: 'chain',
			url: 'index.php?option=com_jupgradepro&format=raw&task=ajax.step&extensions=tables',
			method: 'get'
		}); // end Request		


		ext_step.addEvents({
			'complete': function(response) {

				var object = JSON.decode(response);

				// Redirect if total == 0
				if (object.total == 0) {
					if (object.end == 1) {
						pb7.finish();
						this.cancel();
						self.done();
					}else{
						ext_step.send();
					}
				}

				if (self.options.debug == 1) {
					console.log(response);
					ext_text.innerHTML = ext_text.innerHTML + '<br><br>==========<br><b>[STEP: '+object.name+']</b><br><br>' +response;
				}

				// Changing title and statusbar
				//pb7.set(object.id*6);
				ext_status.innerHTML = 'Migrating ' + object.name;
				if (object.middle != true) {
					ext_currItem.innerHTML = object.cid;
				}
				ext_totalItems.innerHTML = object.total;

				// Start the checks
				ext_row.options.url = 'index.php?option=com_jupgradepro&format=raw&task=ajax.migrate&extensions=tables&table='+object.name;	

				// Running the request[s]
				if (method == 'ajax') {
					if (object.total != 0) {
						ext_row.send();
					}
				} else if (method == 'rest') {
					for (i=object.start;i<=object.stop;i++) {
						var reqname = object.name+i;
						ext_rm.addRequest(reqname, ext_row);
					}
					ext_rm.runAll();
				}
			}
		});

		//
		// Initialize the checks
		//
		var check = new Request({
			link: 'chain',
			url: 'index.php?option=com_jupgradepro&format=raw&task=ajax.extensions',
			method: 'get'
		}); // end Request

		// Adding event to the row request
		check.addEvents({
			'complete': function(response) {

				if (response == 1) {
					// CSS stuff
					$('extensions').setStyle('display', 'block');

					ext_step.send();
				}else if (response == 0){
					pb7.finish();
					this.cancel();
					self.done();					
				}
			}
		});

		// Run the check
		check.send();

	}, // end function

	/**
	 * Run the files copying
	 *
	 * @return	bool
	 * @since	1.2.0
	 */
	files: function(e) {
		var self = this;

		var method = self.options.method;

		if (method == 'database') {
			method = 'ajax';
		}

		// CSS stuff
		$('files').setStyle('display', 'block');

		var pb5 = new dwProgressBar({
			container: $('pb5'),
			startPercentage: 10,
			speed: 1000,
			boxID: 'pb5-box',
			percentageID: 'pb5-perc',
			displayID: 'text',
			displayText: false
		});

		// Get the status element
		status = document.getElementById('files_status');
		// Get the migration_text element
		migration_text = document.getElementById('files_text');
		// Get the currItem element
		currItem = document.getElementById('files_currItem');
		// Get the totalItems element
		totalItems = document.getElementById('files_totalItems');

		// Declare counter
		var counter = 0;

		var rm = new Request.Multiple({
			onRequest : function() {
				//console.log('RM request init');
			},
			onComplete : function() {
				//console.log('RM complete');
			}
		});

		//
		// basename function
		//
		basename = function(path) {
			return path.replace(/.*\/|\.[^.]*$/g, '');
		}

		//
		// 
		//
		var file = new Request({
			link: 'chain',
			method: 'get'
		}); // end Request
	
		//
		// 
		//
		var step = new Request({
			link: 'chain',
			url: 'index.php?option=com_jupgradepro&format=raw&view='+method+'&task=imageslist',
			method: 'get'
		}); // end Request		

		step.addEvents({
			'complete': function(response) {
				//console.log(response);
				var object = JSON.decode(response);
				var counter = 0;

				// Changing title and statusbar
				status.innerHTML = 'Getting image list';
				currItem.innerHTML = 0;
				totalItems.innerHTML = object.total;

				// Adding event to the row request
				file.addEvents({
					'complete': function(response) {
						//console.log(response);
						counter = counter + 1;
						currItem.innerHTML = counter;
						status.innerHTML = 'Getting ' + basename(object.images[counter]);						

						percent = (counter / object.total) * 100;

						pb5.set(percent);

						if (counter == object.total) {
							self.done();
						}
						
					}
				});
				
				// Start the checks
				file.options.url = 'index.php?option=com_jupgradepro&format=raw&view='+method+'&task=image&files=images';			
				
				for (i=1;i<=object.total;i++) {
					rm.addRequest(i, file);			
				}
		
				rm.runAll();			
			}
		});

		step.send();

		// Scroll the window
		var myScroll = new Fx.Scroll(window).toBottom();

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
