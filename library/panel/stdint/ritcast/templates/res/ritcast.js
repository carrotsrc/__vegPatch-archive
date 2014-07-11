/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
function RitcastInterface(pid, cid, iid, gid) {
	this.check = function () {
		alert("Called from ritcast"+this._pnid);
	}

	this.poolcheck = function () {
		if(this._group['ripool'] == undefined)
			return;

		this._group['ripool'].check();
	}
};

RitcastInterface.prototype = new KitJS.PanelInterface('ritcast');
