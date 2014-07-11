<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	class chroLibrary
	{
		static public function timeDifference($datetime)
		{
				date_default_timezone_set('UTC');
				$dt = explode(" ",$datetime);
				$date = explode("-", $dt[0]);
				$time = null;
				if(isset($dt[1]))
					$time = explode(":", $dt[1]);

				$timestamp = mktime($time[0], $time[1], $time[2], $date[1], $date[2], $date[0]);
				$current = time();
				var_dump($current);
				var_dump($timestamp);
				$dif = $current - $timestamp;
				var_dump($dif);
		}
	}
?>
