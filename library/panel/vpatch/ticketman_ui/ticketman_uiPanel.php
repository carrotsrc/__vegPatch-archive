<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	class ticketman_uiPanel extends Panel
	{
		private $mode;
		public function __construct()
		{
			parent::__construct();
			$this->setModuleName('ticketman_ui');
			$this->mode = 0;
		}

		public function loadTemplate()
		{
			$this->fallback();
			switch($this->mode) {
			case 0:
				$this->includeTemplate("template/main.php");
			break;

			case 1:
				$this->includeTemplate("template/focus.php");
			break;
			}
		}

		public function initialize($params = null)
		{
			$id = 0;
			if(isset($_GET['vtmi'])) {
				$this->mode = 1;
				$id = $_GET['vtmi'];
			}

			if($this->mode == 0)
				$this->addComponentRequest(1, 101);
			else
			if($this->mode == 1) {
				$this->addComponentRequest(3, array('vtmi' => $id));
				$this->addComponentRequest(4, array('vtmi' => $id));
			}
			parent::initialize();
		}
		public function applyRequest($result)
		{

			foreach($result as $rs) {
				switch($rs['jack']) {
				case 1:
					if($rs['result'] == 104)
						break;

					$this->addTParam('tickets', $rs['result']);
				break;

				case 3:
					$this->addTParam('details', $rs['result']);
				break;

				case 4:
					if($rs['result'] == 104)
						break;

					$this->addTParam('replies', $rs['result']);
				break;
				}
			}
		}

		public function setAssets()
		{
			$this->addAsset('css', "/.assets/general.css");
			$this->addAsset('css', "/.assets/vpatch_table.css");
		}

		private function fallback()
		{
			$qstr = QStringModifier::modifyParams(array('vtmi' => null));
			$this->addFallbackLink('focus', $qstr);

			$spath = SystemConfig::appRelativePath(Managers::AppConfig()->setting('submitrequest'));
			$aid = Session::get('aid');
			$area = Managers::ResourceManager()->getHandlerRef($aid);
			$qstr = "$spath?cpl=$area/{$this->componentId}/{$this->instanceId}/2&dbm-redirect=1";
			$this->addFallbackLink('update', $qstr);

			$qstr = "$spath?cpl=$area/{$this->componentId}/{$this->instanceId}/5&dbm-redirect=1";
			$this->addFallbackLink('reply', $qstr);
		}
	}
?>
