<?php
/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	class vp_helloworldComponent extends Component
	{
		public function initialize()
		{

		}

		public function createInstance($params = null)
		{
			$resManager = Managers::ResourceManager();
			$rids = $resManager->queryAssoc("Instance()<Component('vp_helloworld');");
			if(!$rids)
				return 1;

			return sizeof($rids)+1;
		}

		public function run($channel = null, $args = null)
		{
			$response = null;

			switch($channel)
			{
			}

			if($args == null)
				echo $response;

			 return $response;
		}
	}
?>
