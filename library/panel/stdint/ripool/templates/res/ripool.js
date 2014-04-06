/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
function RipoolInterface() {

	var self = this;
	self.tabpage = null;
	self.rescast = new Array();
	self.viewState = null;
	self.selectedItem = null;

	this.initialize = function () {
		self.tabpage = new KTSet.TabPage(0);
		this.requestTypecast(this.onresponseTypecast);
		this.requestTypecast(this.onresponseTypecast);
		this.requestPool(this.onresponsePool);


		this.modifyLink('next', this.nextPage);
		this.modifyLink('prev', this.prevPage);
		this.modifyLink('addnew', this.modeAddNew);
		var u = new KTSet.NodeUtl(this._pmod+'-table');
		u.gotoChild(2);
		var n = u.numChildren('tr');

		for(var i = 0; i < n; i++)
			this.modifyLink('el'+i, function () {
							var a = this.id.substr(10, (this.id.length-9));
							self._loop.modeViewResource(parseInt(a));
						}
			);
	}

	this.requestCheck = function (token, callback) {
		this.request(0, "token="+token, callback);
	}

	this.check = function (token) {
		console.log(self._loop._pmod+" token: " + token);
	}

	this.checkT = function (token) {
		console.log("Token V2: " + token);
	}

	this.requestTypecast = function () {
		this.request(1, null, this.onresponseTypecast);
	}

	this.onresponseTypecast = function (reply) {
		console.log("Typecast:\n"+reply);
		self.rescast = KitJS.Ajax.responseToArray(reply);
	}

	this.requestPool = function (callback) {
		var params = "rpp=" + self.tabpage.getPage() + "&rpc=" + self.tabpage.getCount();
		this.request(2, params, callback);
	}

	this.onresponsePool = function (reply) {
		console.log("Pool:\n"+reply);
		var tab = KitJS.Ajax.responseToArray(reply);
		self.tabpage.updateTable(tab);
	}

	this.onresponseRemoveResource = function (reply) {
		if(reply == "102")
			console.log("Removed resource");
		else
			console.error("Could not remove resource");

		var tpanel = self._loop._group.tpanel;
		if(tpanel != undefined)
			tpanel.updatePanel(null, null);

		self._loop.requestPool(self._loop.updatePage);
	}

	this.nextPage = function () {
		if(!self.tabpage.nextPage())
			return;
		self._loop.requestPool(self._loop.updatePage);
	}

	this.prevPage = function () {
		if(!self.tabpage.prevPage())
			return;

		self._loop.requestPool(self._loop.updatePage);
	}

	this.resolveType = function (id) {
		var sz = self.rescast.length;

		for(var i = 0; i < sz; i++)
			if(self.rescast[i][0] == id)
				return self.rescast[i][1];

		return "Unk"+id;
	}

	this.modeViewResource = function (id) {
		if(id != undefined)
			self.selectedItem = self.tabpage.getRow(id);

		var r = generateViewResource();
		
		var tpanel = self._loop._group.tpanel;
		if(tpanel != undefined)
			tpanel.updatePanel('View Resource', r);
		
	}

	this.updatePage = function (reply) {
		var tab = KitJS.Ajax.responseToArray(reply);
		self.tabpage.updateTable(tab);
		console.log("FirstID: "+tab[0][0]);
		self._loop.refresh();
	}

	this.refresh = function () {
		var container = this.clearTable();
		this.generateTable(container);
	}

	this.clearTable = function () {
		var elTable = document.getElementById('ripool'+this._pnid+'-table');

		if(elTable == undefined) {
			console.error("No Pool Table");
			return;
		}

		var elContainer = elTable.parentNode;
		if(elContainer == null || elContainer == undefined)
			return null;

		elContainer.removeChild(elTable);
		return elContainer;
	}

	this.generateTable = function (container) {
		var r = document.createDocumentFragment();
		var u = KTSet.NodeUtl(r);
		var sz = self.tabpage.tabSize();
		var row = null;

		u.appendChild('table').id = this._pmod+'-table';
		u.gotoLast();
		u.node.style.width='100%';


		for(var  i = 0; i < sz; i++) {
			row = self.tabpage.getRow(i);
			u.appendChild('tr');
			u.gotoLast();

			u.appendChild('td');
			u.gotoLast();
			u.node.className = 'xsmallfont';
			u.node.style.textAlign = 'right'
			u.appendText(this.resolveType(row[1])+'(');
			u.gotoParent();

			u.appendChild('td');
			u.gotoLast();
			u.node.style.textAlign = 'center';
			u.appendChild('a');
			u.gotoLast();
			u.node.href = 'javascript: void(0)';
			u.node.id = this._pmod + '-el'+i;
			u.node.onclick = function () {
						var n = this.id.substr(10, (this.id.length-9));
						self._loop.modeViewResource(n);
					}

			u.appendText(row[3]);
			u.gotoParent();
			u.gotoParent();

			u.appendChild('td').className = 'xsmallfont';
			u.gotoLast();
			u.appendText(")");
			u.gotoParent();

			u.gotoParent();
		}

		container.insertBefore(r, null);
	}

	generateAddNew = function () {
		var r = document.createDocumentFragment();
		var u = KTSet.NodeUtl(r);
		var e = u.appendChild('div');
		e.className = 'kr-form-group';
		u.node = e;

		u.appendText("Type");
		u.appendChild('br');
		e = KTDoc.Select.open('ripool'+self._loop._pnid+"-restype");
		var sz = self.rescast.length;
		for(var i = 0; i < sz; i++)
			e.insertBefore(KTDoc.Select.option(self.rescast[i][1], self.rescast[i][0]), null);

		e.style.width='120px';
		u.appendChild(e);

		u.gotoParent();
		e = u.appendChild('div');
		e.className = 'kr-form-group';
		u.node = e;

		u.appendText("Label:");
		u.appendChild('br');
		e = u.appendChild('input');
		e.type = "text";
		e.style.width = '120px'
		e.id = self._loop._pmod+"-reslabel";

		u.gotoParent();
		e = u.appendChild('div');
		e.className = 'kr-form-group xsmallfont';
		u.gotoLast();

		u.appendText('Ref (opt):');
		u.appendChild('br');
		e = u.appendChild('input');
		e.type = "text";
		e.style.width = '70px';
		e.value = "0";
		e.id = self._loop._pmod+"-resref";

		u.gotoParent();
		e = u.appendChild('div');
		e.style.marginTop = '15px';
		u.gotoLast();
		e = u.appendChild('input');
		e.type = 'button';
		e.value = 'Add';
		e.className = 'rfloat';
		e.onclick = self._loop.addResource;

		e = u.appendChild('input');
		e.type = 'button';
		e.value = 'Cancel';
		e.className = 'rfloat xsmallfont';
		e.style.marginRight = '5px';
		e.onclick = self._loop.closeAddNew;
		u.gotoParent();

		return r;
	}

	generateViewResource = function () {
		var row = self.selectedItem;

		if(row == null)
			return null;

		var r = document.createDocumentFragment();
		var u = new KTSet.NodeUtl(r);
		u.appendChild('div').className = 'kr-form-group';
		u.gotoLast();
		u.node.style.width = '150px';
		u.appendChild('div').className = 'xsmallfont';
		u.gotoLast();
		u.appendText(self._loop.resolveType(row[1]));

		u.gotoParent();
		u.appendChild('div');
		u.gotoLast();
		u.appendText("("+row[3]+")");
		u.appendChild('br');
		u.appendText(":" + row[2]);

		u.gotoParent();
		u.appendChild('div').className='kr-form-group';
		u.appendChild(KTDoc.Link("Edit",function () { self._loop.modeEdit(); }));

		return r;
	}

	generateEditResource = function () {
		var row = self.selectedItem;

		if(row == null)
			return null;

		var r = document.createDocumentFragment();
		var u = new KTSet.NodeUtl(r);
		var e = null;

		u.appendChild('div').className = 'kr-form-group';
		u.gotoLast();
		u.node.style.width = '170px';
		e = KTDoc.Select.open('ripool'+self._loop._pnid+"-edittype");
		var sz = self.rescast.length;
		for(var i = 0; i < sz; i++) {
			e.insertBefore(KTDoc.Select.option(self.rescast[i][1], self.rescast[i][0]), null);
		}

		e.className = 'xsmallfont';
		u.appendChild(e);
		u.appendChild('br');

		e = u.appendChild('input');
		e.type = 'text';
		e.value = row[3];
		e.id = 'ripool'+self._loop._pnid+"-editlabel"
		e.style.width = '120px';

		u.appendChild('br');
		e = u.appendChild('input');
		e.type = 'text';
		e.value = row[2];
		e.id = 'ripool'+self._loop._pnid+"-editref"
		e.style.width = '30px';

		u.gotoParent();
		u.appendChild('div').className = 'kr-form-group';

		u.gotoLast();
		e = u.appendChild(KTDoc.Link("Remove", function () { self._loop.removeResource(row[0]); }));
		e.className = 'lfloat xsmallfont';
		u.appendChild('br');
		e = u.appendChild(KTDoc.Link("Cancel", function () { self._loop.modeViewResource(); }));
		e.className = 'lfloat';
		e = u.appendChild(KTDoc.Link("Edit", function () { self._loop.editResource(row[0]); }));
		e.className = 'rfloat';

		return r;
	}

	this.modeEdit = function () {
		var e = generateEditResource();

		var tpanel = self._loop._group.tpanel;
		if(tpanel != undefined)
			tpanel.updatePanel('Edit Resource', e);
	}

	this.modeAddNew = function () {
		var e = generateAddNew();
		var tpanel = self._loop._group.tpanel;
		if(tpanel != undefined)
			tpanel.updatePanel('Add New Resource', e);
	}

	this.closeAddNew = function () {
		var tpanel = self._loop._group.tpanel;
		if(tpanel != undefined)
			tpanel.updatePanel(null, null);
	}

	this.addResource = function () {
		var e = document.getElementById(self._loop._pmod+"-restype");
		if(e == undefined)
			return;
		var type = e.value;

		var e = document.getElementById(self._loop._pmod+"-reslabel");
		if(e == undefined)
			return;
		var label = e.value;

		var e = document.getElementById(self._loop._pmod+"-resref");
		if(e == undefined)
			return;
		var ref = e.value;
		var post = "type="+type+"&label="+label+"&ref="+ref;
		console.log(post);
		self._loop.request(4,null,self._loop.onresponseAddResource, post);
	}

	this.removeResource = function (id) {
		var post = "id="+id;
		if(confirm("Are you sure you want to remove this resource?"))
			self._loop.request(5, null, self._loop.onresponseRemoveResource, post);
	}

	this.editResource = function () {
		var e = document.getElementById(self._loop._pmod+'-edittype');
		if(e == undefined)
			return;
		var type = e.value;
		
		e = document.getElementById(self._loop._pmod+'-editlabel');
		if(e == undefined)
			return;
		var label = e.value;
		
		e = document.getElementById(self._loop._pmod+'-editref');
		if(e == undefined)
			return;
		var ref = e.value;

		var id = self._loop.selectedItem[0];

		var post = "id="+id+"&type="+type+"&label="+label+"&ref="+ref;
		self._loop.request(6, null, self._loop.onresponseModifyResource, post);
	}

	this.onresponseAddResource = function (reply) {
		console.log(reply);
	}

	this.onresponseModifyResource = function (reply) {
		var tpanel = self._loop._group.tpanel;
		if(tpanel != undefined)
			tpanel.updatePanel(null, null);
		self._loop.requestPool(self._loop.updatePage);
	}
};

RipoolInterface.prototype = new KitJS.PanelInterface('ripool');

