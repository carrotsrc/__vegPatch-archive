<?php
/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	require_once("pluginman.php");
	class Channel
	{
		protected $pluginStack;
		
		public function __construct()
		{
			$this->pluginStack = array();
		}
		
		public function addPlugin($plugin)
		{
			$this->pluginStack[] = $plugin;
		}
		
		public function runSignal(&$signal)
		{
			$msgBox = array();
			foreach($this->pluginStack as $plugin)
				if(!($signal = $plugin->process($signal)))
					return false;
			
			return $signal;
		}
	}

?>
