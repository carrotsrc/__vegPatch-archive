<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	class isrootPlugin extends Plugin
	{
		private $resourceManager;
		public function init($instance)
		{
			$this->instance = $instance;
			$this->resManager = Managers::ResourceManager();
		}

		public function process(&$params)
		{
			$uid = Session::get('uid');
			if($uid == null) {
				die("Not sure you should be peeking around in here");
			}

			$res = $this->resManager->queryAssoc("User('$uid')<Role('root');");
			
			if(!$res) {
				KLog::error("Root access violation");
				die("Not sure you should be peeking around in here");
			}

			return $params;
		}

	}
?>
