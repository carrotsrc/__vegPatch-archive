/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
function TPanelInterface() {
	var self = this;

	this.initialize = function () {
		return;
		self.title = null;
		self.body = null;
	}

	this.setPanel = function (title, body) {
		self.title = title;
		self.body = body;
	}

	this.updatePanel = function (title, body) {
		this.setPanel(title, body);
		this.refresh();
	}

	this.refresh = function () {


		var eid = "tpanel"+self._loop._pnid+"-title";
		var e = KTSet.NodeUtl(eid);
		if(e == undefined)
			return;

		e.clearChildren();

		if(self.title != null)
			e.appendText(self.title);

		var eid = "tpanel"+self._loop._pnid+"-body";

		e = KTSet.NodeUtl(eid);
		if(e == undefined)
			return;

		e.clearChildren();
		if(self.body == null)
			e.appendText("Nothing Selected");
		else
			e.appendChild(this.body);
	}
};

TPanelInterface.prototype = new KitJS.PanelInterface('tpanel');

