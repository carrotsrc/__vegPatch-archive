<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	class acachemkPlugin extends Plugin
	{
		public function init($instance)
		{
			$this->instance = $instance;
		}

		public function process(&$params)
		{
			$buf = null;

			if(isset($params['acache']))
				$buf = $params['acache'];
			else
				return $params;

			$cache = Session::get('acache');
			if($cache == null)
				$cache = array('js' => array(), 'css' => array());
			

			if(is_array($params['arc'])) {
				$this->rootAssets($cache, $params['arc']);
				Session::set('arc', 1);
			}

			$cfield = null;
			$width = floor((PHP_INT_SIZE<<3)/3);
			$wmask = pow(2, $width)-1;
			$pmask = $wmask<<$width;
			$pmask ^= $wmask;
			$chk = false;
			foreach($buf as $t => $assets) {
				foreach($assets as $a) {
					$field = $a[1];
					$field <<= $width;
					$field ^= $a[0];

					if($field^$cfield) {
						$chk = false;
						$cfield = $field;
						foreach($cache[$t] as $c)
							if(!(($c[0]&$pmask)^$cfield)) {
								$chk = true;
								break;
							}
					}

					if(!$chk) {
						$field ^= ($a[2]<<($width<<1));
						$cache[$t][] = array($field, $a[3]);
					}
				}
			}
			Session::set('acache', $cache);
			$cache = Session::get('acache');
			return $params;
		}

		private function rootAssets(&$cache, $buf)
		{
			foreach($buf as $t => $list) {
				$sz = sizeof($list)-1;
				for($i = $sz; $i>=0; $i--)
					array_unshift($cache[$t], array(0, $list[$i]));
			}
		}
	}
?>
