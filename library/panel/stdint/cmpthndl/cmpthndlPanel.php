<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	class cmpthndlPanel extends Panel
	{
		private $mode;
		public function __construct()
		{
			parent::__construct();
			$this->setModuleName('cmpthndl');
			$this->mode = 0;
		}

		public function loadTemplate()
		{
			switch($this->mode) {
			case 0:
				$this->includeTemplate("templates/basic.php");
			break;

			case 1:
				$this->includeTemplate("templates/addinst.php");
			break;
			}
		}

		public function initialize($params = null)
		{
			$vars = $this->argVar(array( '_cmr' => 'area',
						'chret' => 'rcode', // component return code
						'sichk' => 'killsig',), // kill signal for panel in CRUD
						$params);


			switch($this->crud) {
			case PNL_CM_CREATE:
				$this->mode = 1; // here we add an instance
			break;

			case PNL_CM_REMOVE:
				
			break;
			}

			if($this->mode == 1) {
				$this->fallbackAddMode();
				$this->addMode($vars);
			}
		}

		public function applyRequest($result)
		{
			foreach($result as $rs) {
				switch($rs['jack']) {
				case 1:
					$this->addTParam('types', $rs['result']);
				break;
				}
			}
		}

		public function setAssets()
		{

		}

		public function addMode($vars)
		{
			if($this->crud != PNL_CM_NONE && $vars->killsig == 1 ||
			   $vars->rcode == 102) {
				// we have a crud panel termination
				$this->crud = PNL_CM_TERM;
				return;
			}

			$this->addComponentRequest(1, 101);
		}

		public function fallbackAddMode()
		{
			$spath = SystemConfig::appRelativePath(Managers::AppConfig()->setting('submitrequest'));
			$aid = Session::get('aid');
			$area = Managers::ResourceManager()->getHandlerRef($aid);
			$qstr = "$spath?cpl=$area/{$this->componentId}/{$this->instanceId}/2&dbm-redirect=0";
	//		$qstr = "$spath?cpl=$area/{$this->componentId}/{$this->instanceId}/2";
			$this->addFallbackLink('submit', $qstr);
		}
	}
?>
