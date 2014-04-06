<?php
/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	class bblogposterPanel extends Panel
	{
		public function __construct()
		{
			parent::__construct();
			$this->setModuleName('bblogposter');
		}

		public function loadTemplate()
		{
			$this->includeTemplate("templates/poster.php");
		}

		public function initialize($params = null)
		{
			$spath = SystemConfig::appRelativePath(Managers::AppConfig()->setting('submitrequest'));
			$aid = Session::get('aid');
			$area = Managers::ResourceManager()->getHandlerRef($aid);
			$qstr = "$spath?cpl=$area/{$this->componentId}/{$this->instanceId}/1&dbm-redirect=1";
			$this->addFallbackLink('submit', $qstr);

			parent::initialize();
		}
		public function applyRequest($result)
		{

			foreach($result as $rs) {
				switch($rs['jack']) {
					

				}
			}
		}

		public function setAssets()
		{

		}
	}
?>
