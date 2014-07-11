/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
var ResourceBin = {
	self: this,
	bin: Array(),
	register: function (obj) {
		ResourceBin.bin.push(obj);
	},

	requestRefresh: function () {
		var sz = ResourceBin.bin.length;

		for(var i = 0; i < sz; i++) {
			ResourceBin.bin[i].refresh();
		}
	}
}
