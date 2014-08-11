<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	class acacheldPlugin extends Plugin
	{
		private $fm;
		private $width;
		private $wmask;
		private $amask;

		public function init($instance)
		{
			if($instance == null)
				return false;

			$this->setInstance($instance);
			$this->fm = new FileManager();


			$this->width = floor((PHP_INT_SIZE<<3)/3);
			$this->wmask = pow(2, $this->width)-1;
			return true;
		}

		public function process(&$signal)
		{
			$type = $panel = 0;
			$sz = sizeof($signal['lcr']);

			if($sz == 1) // we have a batch request
				$type = $signal['lcr'][0];
			else
			if($sz == 2) { // we have a single request 
				$panel = $signal['lcr'][0];
				$type = $signal['lcr'][1];
			}
		
			$cache = Session::get('acache');

			if($cache == null) {
				$signal['response'] = "";
				return $signal;
			}

			ob_start();
			$this->loadFromCache($cache, $signal['area'], $signal['layout'], $type);
			$signal['response'] = ob_get_contents();
			ob_end_clean();
			HttpHeader::fromType($type);
			return $signal;
		}

		private function loadFromCache($cache, $area, $layout, $type)
		{
			if($cache == null)
				return "";
			$gpath = SystemConfig::libraryPath().Managers::AppConfig()->setting('globalasset')."/";
			$lpath = SystemConfig::LibraryPath();
			$path = null;

			$check = ($layout<<$this->width);
			$check ^= $area;
			$mask = $this->wmask<<$this->width;
			$mask ^= $this->wmask;
			foreach($cache[$type] as $asset) {

				$m = $asset[0]&$mask;
				switch($m) {
				case $check:
				case $area:
					$path = $lpath;
				break;
				case 0:
					$path = $gpath;
				break;
				default:
					$path = null;
				break;
				}

				if($path == null)
					continue;

				$ex = explode("/", $asset[1]);
				$name = end($ex);
				unset($ex);
				
				echo "\n\n/* $name */\n\n";
				if(!($f = $this->fm->openFile($path.$asset[1], "r")))
					echo "/* CACHE PATH ERROR */\n";
				else
					echo $f->read();
				
			}
		}
	}

?>
