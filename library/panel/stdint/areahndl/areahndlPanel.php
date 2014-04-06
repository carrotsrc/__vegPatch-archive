<?php
/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	class areahndlPanel extends Panel
	{
		private $mode;
		private $stage;
		private $addName;
		private $addSurround;
		private $addTemplate;
		private $surrounds;
		public function __construct()
		{
			parent::__construct();

			$this->setModuleName('areahndl');
			$this->jsCommon = null;
			$this->mode = 0; // view
			$this->stage = 0; // beginning of add
		}

		public function setAssets()
		{
			$this->addAsset("js", "templates/ah.js");
			$this->addAsset("css", "templates/ah.css");
		}

		public function initialize($params = null)
		{
			switch($this->crud) {

			case PNL_CM_READ:
				$this->mode = 0;
			break;

			case PNL_CM_CREATE:
				$this->mode = 1;
			break;

			case PNL_CM_DELETE:
				$this->mode = 2;
			break;

			}

			$vars = $this->argVar(array( '_cmr' => 'area',
						'siahs' => 'stage',
						'ahret' => 'rcode', // component return code
						'siahk' => 'killsig',), // kill signal for panel in CRUD
						$params);

			switch($this->mode) {
			case 0:
				$this->fallbackViewMode();
				$this->loadArea($vars);
			break;

			case 1:
				$this->addNew($vars);
				$this->fallbackAddMode();
			break;

			case 2:
				$this->fallbackRemoveMode();
				$this->removeArea($vars);
			break;
			}
			return true;
		}

		private function loadArea($vars)
		{
			if($vars->area == null)
				return;

			$this->addComponentRequest(3, array('siahi' => $vars->area));
		}

		private function addNew($vars)
		{
			if($this->crud != PNL_CM_NONE && $vars->killsig == 1 ||
			   $vars->rcode == 102) {
				// we have a crud panel termination
				$this->crud = PNL_CM_TERM;
				return;
			}

			if($vars->stage != null)
				$this->stage = $vars->stage;
				
			switch($this->stage) {
			case 0:
				$this->addComponentRequest(5, 101);
			break;
			case 1:
				$this->addName = $_POST['siahl'];

				$this->addSurround = $_POST['siahs'];

				$this->addComponentRequest(6, array('siahs' => $this->addSurround));
				if($this->addName == '')
					$this->addName = null;
				else
					$this->stage = 2;
				
				// we are getting data from post
				$this->addTParam('name', $this->addName);
				$this->addTParam('sid', $this->addSurround);
			break;
			}
		}

		private function removeArea($vars = null)
		{
			if($this->crud != PNL_CM_NONE && $vars->killsig == 1 ||
			   $vars->rcode == 102) {
				$this->crud = PNL_CM_TERM;
			}
			if($vars->area == null)
				return;
			
			$this->addComponentRequest(3, array('siahi' => $vars->area));
		}

		public function loadTemplate()
		{
			switch($this->mode) {
			case 0:
				$this->includeTemplate("templates/view.php");
			break;

			case 1:
				$this->includeTemplate("templates/add.php");
			break;

			case 2:
				$this->includeTemplate("templates/remove.php");
			break;
			}
		}

		public function applyRequest($result)
		{
			foreach($result as $rs)
				switch($rs['jack']) {
				case 3:
					$this->addTParam('area', $rs['result'][0]);
				break;

				case 5:
					$this->addTParam('surrounds', $rs['result']);
				break;

				case 6:
					$this->addTParam('templates', $rs['result']);
				break;
				}
		}

		public function fallbackViewMode()
		{
		}

		public function fallbackAddMode()
		{
			switch($this->stage) {
			case 0:
			case 1:
				$qstr = QStringModifier::modifyParams(array('siahs' => 1, 'ahret' => null));
			break;
			case 2:
				$spath = SystemConfig::appRelativePath(Managers::AppConfig()->setting('submitrequest'));
				$aid = Session::get('aid');
				$area = Managers::ResourceManager()->getHandlerRef($aid);
			//	$qstr = "$spath?cpl=$area/{$this->componentId}/{$this->instanceId}/1&dbm-redirect=0";
				$qstr = "$spath?cpl=$area/{$this->componentId}/{$this->instanceId}/1";
			break;
			}

			$this->addFallbackLink('next', $qstr);

			$qstr = QStringModifier::modifyParams(array('siahs' => null, 'ahret' => null, 'siahk' => 1));
			$this->addFallbackLink('cancel', $qstr);
		}

		public function getUrlParams()
		{
			$globParams = parent::getUrlParams();
			// include the area handler return code
			$stuff = array_merge($globParams, array('siahs', 'siahi', 'siaho', 'siahk' ,'ahret'));
			return $stuff;
		}

		public function fallbackRemoveMode()
		{
			$spath = SystemConfig::appRelativePath(Managers::AppConfig()->setting('submitrequest'));
			$aid = Session::get('aid');
			$area = Managers::ResourceManager()->getHandlerRef($aid);
	//		$qstr = "$spath?cpl=$area/{$this->componentId}/{$this->instanceId}/2&dbm-redirect=0";
			$qstr = "$spath?cpl=$area/{$this->componentId}/{$this->instanceId}/2";
			$this->addFallbackLink('submit', $qstr);

			$qstr = QStringModifier::modifyParams(array('siaho'=>null, 'siahs'=>null, 'ahret'=>null, 'siahk' => 1));
			$this->addFallbackLink('cancel', $qstr);
		}
}
?>
