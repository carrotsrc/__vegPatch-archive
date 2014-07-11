<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	require_once("pluginman.php");
	class Channel
	{
		protected $pluginStack;
		protected $open;
		
		public function __construct()
		{
			$this->pluginStack = array();
			$this->open = true;
		}
		
		public function addPlugin($plugin)
		{
			$this->pluginStack[] = $plugin;
		}
		
		public function runSignal(&$params)
		{
			$msgBox = array();
			foreach($this->pluginStack as $plugin)
				if(!($params = $plugin->process($params)))
					return false;
			
			return $params;
		}
	}

?>
