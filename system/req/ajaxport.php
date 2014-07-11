<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
error_reporting(E_ALL);
ini_set('display_errors', '1');

	if(!require($_SERVER['DOCUMENT_ROOT'] . "/ksysconfig.php"))
		die("Setup Problem: Cannot locate SystemConfig");

	SystemConfig::$KS_FLAG = KS_PLUGIN | KS_SESSION | KS_MOD;

	
	include_once(SystemConfig::appRootPath("system/app.php"));

	$app = new App();

	if(!$app->init(false, false))
		die("Application failed to initialize!");

	$result = $app->requestInterface();
	if(!$result)
		echo "Err: failed to load component";

	echo $result;
?>
