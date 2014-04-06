<?php
/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	if(!include($_SERVER["DOCUMENT_ROOT"]."/ksysconfig.php"))
		die("Setup Problem: Cannot locate SystemConfig.");
	

	$swType = null;
	$swId = null;
	$uid = null;
	$aName = null;
	$aType = null;
	
	if(!isset($_GET['loc']))
		die("Err: No asset specified");

	SystemConfig::$KS_FLAG = KS_ASSETS;
	include_once(SystemConfig::appRootPath("system/app.php"));

	$app = new App();

	if(!$app->init(false, false))
		die("/*Major Malfunction: Application failed to initialize! */");

	$contents = $app->getAsset();
	
	if($contents == null)
		die("/* ERR - contents is NULL */");
		
	echo $contents;
?>
