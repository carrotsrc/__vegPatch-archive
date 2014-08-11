<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	function url_modify_params($params = array())
	{
		$np = $_GET;

		foreach($params as $p => $v)
			if($v == null)
				unset($np[$p]);
			else
				$np[$p] = $v;

		return http_build_query($np);
	}
?>
