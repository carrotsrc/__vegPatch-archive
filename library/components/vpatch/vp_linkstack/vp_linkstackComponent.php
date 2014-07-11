<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	class vp_linkstackComponent extends Component
	{
		private $resManager;
		public function initialize()
		{
			$this->resManager = Managers::ResourceManager();

		}

		public function run($channel = null, $args = null)
		{
			$response = null;

			switch($channel) {
			case 1:
				$response = $this->getValidAreas();
			break;
			}

			if($args == null)
				echo $response;

			 return $response;
		}

		public function getValidAreas()
		{
			$uid = Session::get('uid');
			$access = array();
			// is user root
			$rid = $this->resManager->queryAssoc("User('$uid')<Role('root');");
			if(!$rid)
				$access['root'] = false;
			else {
				$access['root'] = true;
				return $access;
			}

			$rid = $this->resManager->queryAssoc("User('$uid')<Course():cadmin;");
			if(!$rid)
				$access['cadmin'] = false;
			else
				$access['cadmin'] = true;

			return $access;
		}

	}
?>
