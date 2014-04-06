<?php
/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
error_reporting(E_ALL);
ini_set('display_errors', '1');

	if(!require($_SERVER['DOCUMENT_ROOT'] . "/ksysconfig.php"))
		die("Setup Problem: Cannot locate SystemConfig");

	SystemConfig::$KS_FLAG = KS_PLUGIN | KS_SESSION | KS_MOD | KS_DEBUG_MICRO | KS_SURROUND;
	include_once(SystemConfig::appRootPath("system/app.php"));

	$app = new App();

	if(!$app->init(false, false))
		die("Application failed to initialize!");
	foreach($_GET as $key => $value)
		if(isset($track[1][$key]))
			$track[1][$key] = $value;


	$result = $app->requestInterface();
	echo $result;
	/*
	*  Make alterations to the query string
	*/

$track = Session::get('track');
$location = $track[0]."?".http_build_query($track[1]);

if(SystemConfig::$KS_FLAG&KS_DEBUG_MICRO) {
	if(isset($_GET['dbm-redirect']) && $_GET['dbm-redirect'] == 0)
		die("<p><b>Halted Redirect</b><br />$location</p>");
}
	HttpHeader::redirect($location);
?>
