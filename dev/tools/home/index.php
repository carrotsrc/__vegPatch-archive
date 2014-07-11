<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	
	if(!defined('_ROOT_TOOL'))
		die("Not logged in");

	include(SystemConfig::relativeAppPath("system/resource/resman.php"));
	
	$fm = Koda::getFileManager();
	$rman = new ResMan($db);
	$panel = "";

?>
	<div id="kr-layout" style="padding: 15px;">
		<span class="ok-text-blue"><span class="tx-large"><b>Root Tools</b></span><br />
		<b>Platform Version:</b><span class="ok-text-grey"> <b>0.2.1</b></span></span>
		<div style="margin-top: 15px">
			<div class="tool-icon">
			<a href="?tool=area"><img src="area/icon.png" /><br />Areas</a>
			</div>
			<div class="tool-icon">
			<a href="?tool=assoc"><img src="assoc/icon.png" /><br />Associations</a>
			</div>
			<div class="tool-icon">
			<a href="?tool=channel"><img src="channel/icon.png" /><br />Channels</a>
			</div>
			<div class="tool-icon">
			<a href="?tool=layout"><img src="layout/icon.png" /><br />Layouts</a>
			</div>
			<div class="tool-icon">
			<a href="?tool=module"><img src="module/icon.png" /><br />Modules</a>
			</div>
			<div class="tool-icon">
			<a href="?tool=query"><img src="query/icon.png" /><br />Query</a>
			</div>
			<div class="tool-icon">
			<a href="?tool=resource"><img src="resource/icon.png" /><br />Resources</a>
			</div>
			<div class="tool-icon">
			<a href="?tool=root"><img src="root/icon.png" /><br />Root Config</a>
			</div>
			<div class="tool-icon">
			<a href="?tool=strap"><img src="strap/icon.png" /><br />Straps</a>
			</div>
		</div>
	</div>
