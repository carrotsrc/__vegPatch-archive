var VPLib = {
	Ajax: {
		requestURL: "request.php",
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
		}
	}
}

var KTSet = {

	Node: function (type) {
		var e = document.createElement(type);
		return KTSet.NodeUtl(e);
	},

	NodeUtl: function (ref) {

		this.node = undefined;
		if(typeof ref == 'string')
			this.node = document.getElementById(ref);
		else
			this.node = ref;

		if(this.node == undefined)
			return undefined;

		this.getElementById = function (id) {
			if(!this.node.hasChildNodes())
				return null;

			var c = this.node.firstChild;
			while(c != null) {
				if(id == c.id)
					return c;

				c = c.nextSibling;
			}
		}


		this.clearChildren = function () {
			var c = this.node.firstChild;
			while(c != null) {
				if(c.hasChildNodes())
					recursiveClear(c);

				this.node.removeChild(c);
				c = this.node.firstChild;
			}
		}

		this.appendChild = function (e) {
			if(e == undefined || e == null)
				return;
			if(typeof e == 'string')
				e = document.createElement(e);

			this.node.insertBefore(e, null);

			return e;
		}

		this.appendInput = function (type, value) {
			try {
				this.appendChild('input');
					this.gotoLast();
					this.node.type=type;
					this.node.value = value;
					this.gotoParent();
			}
			catch(e) { // catch IE's tantrum over low standards
				this.gotoParent();
				this.removeChild(this.node.lastChild);
				this.node.innerHTML = this.node.innerHTML + "<input type=\""+type+"\" value=\""+value+"\" onclick=\"\" />";
			}
		}


		this.appendText = function (text) {
			if(text == undefined || text == null)
				return;

			this.node.insertBefore(document.createTextNode(text), null);
		}

		recursiveClear = function (node) {
			var c = node.firstChild;
			while(c != null) {
				if(c.hasChildNodes())
					recursiveClear(c);
				node.removeChild(c);
				c = node.firstChild;
			}
		}

		this.gotoChild = function (child) {
			var c = this.node.firstChild;
			while(c != null && child > 1) {
				c = c.nextSibling;
				child--;
			}
			if(c == null)
				return null;

			this.node = c;
			return this.node;
		}

		this.gotoFirst = function () {
			if(this.node.firstChild == null)
				return null;

			this.node = this.node.firstChild;
			return this.node;
		}

		this.gotoLast = function () {
			if(this.node.lastChild == null)
				return null;

			this.node = this.node.lastChild;
			return this.node;
		}

		this.gotoParent = function () {
			if(this.node.parentNode == null)
				return null;

			this.node = this.node.parentNode;
			return this.node;
		}

		this.numChildren = function (type) {
			if(this.node == undefined)
				return null;

			if(!this.node.hasChildNodes())
				return 0;

			var c = this.node.firstChild;

			var n = 0;
				
			while(c != null) {

				if(c.nodeName.toLowerCase() == type ||
				   type == undefined)
					n++;

				c = c.nextSibling;
			}

			return n;
		}

		this.getChild = function (num) {
			if(num > -1)
				if(num < this.node.childNodes.length)
					return this.node.childNodes[num];
				else
					return undefined;

			var c = this.node.firstChild;
			if(num  == -1) {
				var r;
				while(c != null) {
					r = c;
					c = c.nextSibling;
				}
				return r;
			}
		}

		this.removeChild = function (child) {
			this.node.removeChild(child);
		}

		return this;
	}
}
