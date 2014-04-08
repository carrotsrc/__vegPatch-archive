/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
var KTSet = {
	TabPage: function (idCol) {
		this.page = 1;
		this.pagestart = new Array();

		this.tabstart = 1;
		this.tabend = 1;
		this.tabsize = 0;
		this.tabdata = null;

		this.count = 10;

		this.idCol = idCol;

		this.init = function (idCol) {
			this.idCol = idCol;
		}

		this.nextPageValue = function () {
			if(this.pagestart.length > this.page)
				return this.pagestart[this.page+1];
			else
				return parseInt(this.tabend)+1;
		}

		this.prevPageValue = function () {
			if(this.page == 1)
				return null;

			if(this.pagestart.length < this.page-1) {
				console.error("TabPage : Page start IDs not set correctly");
				return null;
			}

			return this.pagestart[this.page-1];
		}

		this.nextPage = function () {
			this.page++;
			this.tabstart = this.nextPageValue();
			return true;
		}

		this.prevPage = function () {
			//  Theres a bug around here
			if(this.page == 1)
				return false;

//			this.tabstart = this.prevPageValue();
			if(this.tabstart == null)
				return false;

			this.page--;
			return true;
		}

		this.clearTable = function () {
			this.tabdata = null;
			this.tabdata = new Array();
			this.tabsize = 0;
		}

		this.addData = function (data) {
			this.tabdata.push(data);
			this.tabsize++;
			if(this.tabsize == 0)
				this.tabstart = data[this.idCol];

			this.tabend = data[this.idCol];
		}

		this.updateTable = function (data) {
			if(this.idCol < 0) {
				console.error("TabPage : No valid id column specified");
				return false;
			}

			if(data[0].length < this.idCol) {
				console.error("TabPage : Id Column out of bounds");
				return false;
			}

			this.tabdata = data;
			this.tabsize = data.length;
			this.tabstart = data[0][this.idCol];
			this.tabend = data[this.tabsize-1][this.idCol];

			if(this.pagestart.length < this.page)
				this.pagestart.push(this.tabstart);

			return true;
		}

		this.getData = function () {
			return this.tabdata;
		}

		this.getRow = function (id) {
			if(id >= this.tabdata.length)
				return null;

			return this.tabdata[id];
		}

		this.tabStart = function () {
			return this.tabstart;
		}

		this.tabEnd = function () {
			return this.tabend;
		}

		this.tabSize = function () {
			return this.tabsize;
		}

		this.getCount = function () {
			return this.count;
		}

		this.setCount = function (count) {
			this.count = count;
		}

		this.getPage = function () {
			return this.page;
		}
	},

	ArIterator: function (ar) {
		this.data = ar;
		this.size = ar.length;
		this.index = 0;

		this.getNext = function () {
			if(this.index == this.size)
				return null;

			var row = this.data[this.index];
			this.index++;
			return row;
		}

		this.reset = function () {
			this.index = 0;
		}
	},

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

var KTDoc = {
	Table: {
		element: function (id) {
			var e = document.createElement('table');
			e.id = id;
			return e;
		},

		row: function () {
			var tr = document.createElement('tr');
			var sz = arguments.length;
			for(var i = 0; i < sz; i++) {
				var td = document.createElement('td');
				td.insertBefore(document.createTextNode(arguments[i]), null);
				tr.insertBefore(td, null);
			}

			return tr;
		}
	},

	Select: {
		element: function (id) {
			var select = document.createElement('select');
			select.id = id;

			return select;
		},

		option: function (name, value) {
			var option =  document.createElement('option');
			option.value = value;
			option.insertBefore(document.createTextNode(name), null);
			return option;
		}
	},

	Link: function(title, href) {
		var a = document.createElement('a');

		if(typeof href == 'string')
			a.href = href;
		else
		if(href instanceof Function) {
			a.href = 'javascript: void(0)';
			a.onclick = href;
		}

		a.insertBefore(document.createTextNode(title), null);
		return a;
	}
}


