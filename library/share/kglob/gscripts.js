/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
var VPLib = {
	Ajax: {
		requestURL: "__URL_AJAX_REQ__",
		requestOpen: false,
		blocking: false,

		/* TODO
		*  Write code for IE 6
		*  Sort out first in queue bug- always uses
		*  previous callback
		*/
		rxo: function () {
			var self = this;

			self.request = null;

			this.initRequest = function () {
				try {
					self.request = new XMLHttpRequest();
				} catch (e) {
					try {
						self.request = new ActiveXObject("Msxml2.XMLHTTP");
					} catch (e) {
						try {
							self.request = new ActiveXObject("Microsoft.XMLHTTP");
						} catch(e) {
							alert("Failed to create request object!");
						}
					}
				}
			
			}

			this.initRequest();

			self.queue = new Array();
			self.lock = false;
			self.isOpen = false;
			self.valTimeout = 3000;

			this.makeRequest = function (args, callback, post) {
				if(self.isOpen)
					push(args, post, callback);
				else
					open(args, post, callback);
			}


			var push = function (args, post, callback) {
				if(self.lock)
					setTimeout(function () { push(args, post, callback); }, 30);
				else {
					self.lock = true;
					self.queue.push(new Array(args, post, callback));
					self.lock = false;
				}
			}

			var open = function (args, post, callback) {
				self.initRequest();
				self.isOpen = true;
				url = VPLib.Ajax.requestURL;
				if(args != null)
					url += "?"+args;
				if(post == undefined || post == null)
					self.request.open('GET', url, true);
				else
					self.request.open('POST', url, true);

				self.request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

				if(post != undefined && post != null)
					self.request.setRequestHeader('Content-Length', post.length);

				var cfunc = callback;
				self.request.cfunc = cfunc
				self.request.onreadystatechange = function () {
					statechange(this.cfunc);
				}

				self.request.timeout = self.valTimeout;
				self.request.ontimeout = timeout;
				if(post == undefined || post == null)
					self.request.send();
				else
					self.request.send(post);
			}

			var timeout = function () {
				try {
					console.error("Request timed out ("+self.valTimeout+"ms)");
				} catch(e) {
					alert("timed out");
				}
				self.isOpen = false;

				checkQueue();
			}

			var statechange = function (callback) {
				switch(self.request.readyState) {
				case 4:
					callback(self.request.responseText);
					self.request.abort();
					self.isOpen = false;
					checkQueue();
				break;
				}
			}

			var checkQueue = function () {
				if(self.lock)
					setTimeout(function () {checkQueue();}, 30);
				else
				if(self.isOpen)
					setTimeout(function () {checkQueue();}, 30);
				else
				if(self.queue.length > 0) {
					self.lock = true;
					var p = self.queue[0];
					self.queue.splice(0, 1);
					if(p[1] === undefined)
						p[1] = null;
					self.initRequest();
					open(p[0], p[1], p[2]);
					self.lock = false;
				}
			}
		},
		rox: null,

		request: function (args, callback, post) {
			if(VPLib.Ajax.rox == null)
				VPLib.Ajax.rox = new VPLib.Ajax.rxo();

			VPLib.Ajax.rox.makeRequest(args, callback, post);
		},


		responseToArray: function (response) {
			var ar = new Array();
			var rows = response.split("\n");
			var szi = rows.length;
			for(var i = 0; i < szi; i++) {
				if(rows[i] == "")
					continue;
				var cols = rows[i].split(',');
				var szj = cols.length;
				var row = new Array();
				for(var j = 0; j < szj; j++) {
					if(cols[j] != "")
						row.push(cols[j]);
				}

				ar.push(row);
			}

			return ar;
		}
	},

	Asset: {
		loadedList: new Array(),
		requestScript: function (url) {
			var sz = VPLib.Asset.loadedList.length;
			for(var i = 0; i < sz; i++) {
				if(VPLib.Asset.loadedList[i] == url)
					return false;
			}

			var r = document.createDocumentFragment();

			r.insertBefore(document.createElement('script'), null);
				r = r.lastChild;
				r.type = "text/javascript";
				r.src = url;

			var c = document.getElementsByTagName("head");
			c = c[0];
			c.insertBefore(r, null);

			VPLib.Asset.loadedList.push(url);
			return true;
		},

		requestStyle: function (url) {
			var sz = VPLib.Asset.loadedList.length;
			for(var i = 0; i < sz; i++) {
				if(VPLib.Asset.loadedList[i] == url)
					return false;
			}

			var r = document.createDocumentFragment();

			r.insertBefore(document.createElement('link'), null);
				r = r.lastChild;
				r.type = "text/css";
				r.rel = "stylesheet";
				r.href = url;

			var c = document.getElementsByTagName("head");
			c = c[0];
			c.insertBefore(r, null);

			VPLib.Asset.loadedList.push(url);
			return true;
		}
	},

	CommonGroup: {
		groups: {},
		register: function (name, id, group, obj) {
			gname = 'g'+group;
			if(this.groups[gname] == undefined)
				this.groups[gname] = {};
			/*
			*  Create unique ID here
			*/
			if(this.groups[gname][name] == undefined) {
				this.groups[gname][name] = obj;
				obj.setCommonGroup(this.groups[gname]);
			}
			else
				console.error(name + " already exists in group");
		}
	},

	CommonInterface: {
		register: function (pid, cid, iid, gid, cname) {
			if(window[cname] == undefined)
				return;

			var obj = new window[cname]();
			obj.register(pid, cid, iid, gid);

			if(obj['initialize'] != undefined)
				obj.initialize();

			cint = obj._name+pid;
			this[cint] = obj;
		}
	},

	PanelInterface: function (mname) {
		this._name = mname;
		this._pnid = null;
		this._cmpt = null;
		this._inst = null;
		this._gnid = null;
		this._pmod = null;
		this._group = null;
		this._area = '__KAID__';   // Could make this a request with the server
		this._loop = null;
		this._local = null;
		var self = this;

		this.register = function (pid, cid, iid, gid, local) {
			this._pnid = pid;
			this._cmpt = cid;
			this._inst = iid;
			this._gnid = gid;
			this._pmod = this._name+pid;
			this._local = local;
			VPLib.CommonGroup.register(this._name, this._pnid, this._gnid, this);
			self._loop = this; // DEPRECATED
		}

		this.request = function (channel, args, callback, post) {
			var vargs = "cpl="+this._area;
			vargs += "/"+this._cmpt;
			vargs += "/"+this._inst;
			vargs += "/"+channel;
			if(args != null)
				vargs += "&"+args;

			vargs += VPLib.printGlobalParams();
			if(this._local != null) {
				var sz = this._local.length;
				for(var i = 0; i < sz; i++)
					vargs += "&"+this._local[i][0] + "=" + this._local[i][1];
			}

			new VPLib.Ajax.request(vargs, callback, post); 
		}

		this.setCommonGroup = function (group) {
			this._group = group;
		}

		this.modifyLink = function (id, onevent) {
			nid = this._name+this._pnid+'-'+id;
			var e = document.getElementById(nid)
			if(e == undefined)
				return false;

			e.href = "javascript:void(0)";
			e.onclick = onevent;
		}

		this.getElementById = function (id) {
			return document.getElementById(this._pmod+"-"+id);
		}

		this.elementId = function (id) {
			return this._pmod+"-"+id;
		}
	},

	globalParams: new Array(),

	addGlobalParam: function (param, value) {
		VPLib.globalParams.push(new Array(param, value));
	},

	printGlobalParams: function () {
		var sz = VPLib.globalParams.length;
		vargs = "";
		for(var i = 0; i < sz; i++) {
			vargs += "&"+VPLib.globalParams[i][0] + "=" + VPLib.globalParams[i][1];
		}

		return vargs;
	}
}
var KitJS = VPLib;
