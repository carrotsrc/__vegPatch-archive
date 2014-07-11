<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	class DebugMicro
	{
		public function pmu()
		{
			$pmua = $pmu = memory_get_peak_usage();

			if($pmu > 1*pow(10,6))
				$pmu = $pmu/pow(10,6) . " MB";
			else
			if($pmu > 1000)
				$pmu = $pmu/1000 . " KB";
			else
				$pmu .= " B";

			return "<b style=\"font-size: small;\">PMU:</b><br />$pmu<br />$pmua bytes";
		}

		public function ldsess()
		{
			echo "<b style=\"font-size: small\">Session:</b><br />";
			$item = null;
			if(isset($_GET['dbm-session']))
				$item = $_GET['dbm-session'];
			else {
				$str = "";
				$qstr = QStringModifier::modifyParams(array('dbm-session' => '*'));
				$text = "Load";
				$str .= "<a href=\"$qstr\">$text</a>";
				return $str;
			}

			$str = "";
			$qstr = QStringModifier::modifyParams(array('dbm-session' => null));
			$text = "Unload";
			$str .= "<a href=\"$qstr\">$text</a>";

			if($item == "*") {
				
				$r = $str."<pre>".print_r($_SESSION, true)."</pre>";
				return $r;
			}
			$full = $str . $item;
			$atoms = explode(".", $item);
			$item = Session::get($atoms[0]);
			if(is_array($item)) {
				$di = 1;
				$ds = sizeof($atoms)-1;
				$sz = sizeof($item);
				if($ds == 0) {
					return "<b style=\"font-size: small\">{$atoms[0]}:</b><br /><pre>".print_r($item, true)."</pre>";
				}
				
				for($i = 0; $i < $sz; $i++) {
					if(key($item) == $atoms[$di]) {
						$c = current($item);
						if($di == $ds)
							return "<b style=\"font-size: small\">{$full}:</b><br /><pre>".print_r($c, true)."</pre>";

						if(!is_array($c))
							return "Halted - <b style=\"font-size: small\">{$atoms[$di]}:</b><br /><pre>".print_r($c, true)."</pre>";
						
						$di++;
						$i = 0;
						$sz = sizeof($c);
						$item = $c;
					}
					else
						next($item);
				}
			}
			else
				return "<b style=\"font-size: small\">{$atoms[0]}:</b><br /><pre>".print_r($item, true)."</pre>";
		}

		public function cacheNuker()
		{
			$qstr = "";
			$text = "";
			if(isset($_GET['cache']) && $_GET['cache'] != "nuke" || !isset($_GET['cache'])) {
				$qstr = QStringModifier::modifyParams(array('cache' => 'nuke'));
				$text = "Nuke it!";
			}
			
			echo "<b style=\"font-size: small;\">Cache:</b><br />";
			echo "<a href=\"$qstr\">$text</a><br />";
		}

		public function service()
		{
			echo "<b style=\"font-size: small;\">Service:</b>";
			echo "<br /><div style=\"font-size: small; margin-top: 0px;\">Host:</div>";
			echo $_SERVER['HTTP_HOST'];

			echo "<br /><div style=\"font-size: small; margin-top: 7px;\">Name:</div>";
			echo $_SERVER['SERVER_NAME'];

			echo "<br /><div style=\"font-size: small; margin-top: 7px;\">Addr:</div>";
			echo $_SERVER['SERVER_ADDR'];
		}
	}
?>
