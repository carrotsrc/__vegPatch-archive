<?php
/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	class userman_uiPanel extends Panel
	{
		public function __construct()
		{
			parent::__construct();
			$this->setModuleName('userman_ui');
		}

		public function loadTemplate()
		{
			$mpath = SystemConfig::appRelativePath('library/media/general');
			$this->addFallbackLink('mediag',$mpath);
			$this->fallback();
			$this->includeTemplate("template/main.php");
		}

		public function initialize($params = null)
		{
			$this->addComponentRequest(1, 101);
			parent::initialize();
		}
		public function applyRequest($result)
		{

			foreach($result as $rs) {
				switch($rs['jack']) {
				case 1:
					if($rs['result'] == 104)
						break;

					$this->addTParam('users', $rs['result']);
				break;

				}
			}
		}

		public function setAssets()
		{
			$this->addAsset('css', "/.assets/general.css");
		}

		private function fallback()
		{
			$spath = SystemConfig::appRelativePath(Managers::AppConfig()->setting('submitrequest'));
			$aid = Session::get('aid');
			$area = Managers::ResourceManager()->getHandlerRef($aid);
			$qstr = "$spath?cpl=$area/{$this->componentId}/{$this->instanceId}/100&dbm-redirect=1";
			$this->addFallbackLink('pickup', $qstr);
		}
	}
?>
