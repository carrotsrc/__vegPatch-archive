<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

	class KLog {
		static public function error($msg)
		{
			$time = time('now');
			$stamp = date("H:i:s d/m/y", $time);
			$str = "[err]\t$stamp\n\t$msg\n";
			$qstr = http_build_query($_GET);
			$str .= "\n\tqstr: $qstr";
			$uid = Session::get('uid');
			$str .= "\n\tuid: $uid\n";
			error_log($str, 3, KLOG_PATH);
		}
	}
