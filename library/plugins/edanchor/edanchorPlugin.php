<?php
/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	class edanchorPlugin extends Plugin
	{
		private $resourceManager = NULL;
		public function init($instance)
		{
			$this->resourceManager = Managers::ResourceManager();
		}

		public function process(&$signal)
		{
			if($signal['layout'] == 'edit') {
				$rq = "Graph()<Area('{$signal['area']->getName()}');";
				$result = $this->resourceManager->queryAssoc($rq);
				if($result == false)
					$signal['layout'] = null;
				else
					$signal['layout'] = 2;
			}

			return $signal;
		}
	}
?>
