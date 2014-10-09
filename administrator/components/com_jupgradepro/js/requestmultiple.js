/*
Script: Request.Multiple.js
	Controls several instances of Request and its variants to run only one request at a time.

	License:
		MIT-style license.

	Authors:
		IvÃ¡n RodrÃ­guez

	Modified (Mootools 1.3 compatibility):
		Matias Aguirre
		
	Example:
		var rm = new Request.Multiple({
			onRequest : function() {
				console.log('request init');
			},
			onComplete : function() {
				console.log('complete');
			}
		});
		rm.addRequests({
			r1 : new Request({
				url : 'serverAction.php',
				onComplete : function() {
					if(!confirm('quieres seguir?')){
						this.cancel();
					} else {
						console.log('r1 complete');
					}
				}
			}),
	
			r2 : new Request({
				url : 'serverAction.php',
				onComplete : function() {
					if(!confirm('quieres seguir?')){
						this.cancel();
					} else {
						console.log('r2 complete');
					}
				}
			})
		});
		rm.runAll();
*/
Request.Multiple = new Class({
	Implements : [Options, Chain],
	
	options : {
		onRequest : false,
		onComplete : false
	},
	
	initialize : function(options) {
		this.setOptions(options);
		this.requests = new Hash();
	},
			
	runAll : function() {
		var chains = [];
		chains.include(this.request);
		
		Object.each(this.requests, function(request, k) {

			var req = function() {
				request.addEvent('complete', function() {
					this.callChain();
				}.bind(this));
				request.send();
			}.bind(this);
			
			chains.include(req);
			
			this.removeRequest(k);
		}, this);
		
		chains.include(this.complete);			
	
		this.chain(chains);
		this.callChain();
	},
		
	request : function() {
		this.options.onRequest();
		this.callChain();
	},

	complete : function() {
		this.options.onComplete();
	},
		
	addRequests : function(requests) {
		Object.each(requests, function(request, key) {
			this.addRequest(key, request);
		}, this);
	},
	
	addRequest : function(key, request) {			
		this.requests.set(key, request);
	},
	
	removeRequest : function(key) {
		this.requests.erase(key);
	},

	cleanRequests : function(requests) {
		Object.each(requests, function(request, key) {
			this.removeRequest(key);
		}, this);
	}
});
