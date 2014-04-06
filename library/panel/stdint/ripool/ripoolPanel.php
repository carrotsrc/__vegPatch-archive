<?php
/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	/*
	*  TODO:
	*  Get all the direct module interaction
	*  out of here and into a plugin
	*/
	class ripoolPanel extends Panel
	{
		private $resourceManager;

		private $mode;
		private $rpp;
		private $rpe;
		public function __construct()
		{
			parent::__construct();
			$this->setModuleName('ripool');
			$this->jsCommon = 'RipoolInterface';
			$this->resourceManager = null;
			$this->resPool = null;
			$this->mode = 0;
			$this->rpe = 0;
			$this->rpp = 1;
		}

		public function loadTemplate()
		{

			switch($this->mode) {
			case 0:
				$this->includeTemplate("templates/basic.php");
			break;

			case 1:
				$this->includeTemplate("templates/addnew.php");
			break;

			case 2:
				$this->includeTemplate("templates/edit.php");
			break;
			}
		}

		public function setAssets()
		{
			$this->addAsset("js", "/share/riglob.js");
			$this->addAsset("js", "/G/toolset.js");
			$this->addAsset("js", "templates/res/ripool.js");
			$this->addAsset("css", "/share/krstdint.css");
		}

		public function initialize($params = null)
		{
			$this->resourceManager = Managers::ResourceManager();
			$this->fillState();
			switch($this->mode) {
			case 0:
				$this->loadPool(); // view all
			break;

			case 1:
				$this->loadRescast(); // add one
			break;

			case 2:
				$this->loadResource(); // view edit
			break;
			}

			parent::initialize($params);
		}

		private function loadResource()
		{
			$rid = null;
			$rpe = 0;
			if(isset($_GET['stdint-rpi']))
				$rid = $_GET['stdint-rpi'];

			if($rid == null)
				return false;

			$this->addComponentRequest(7, array('rpi' => $rid));

			if(isset($_GET['stdint-rpe']))
				$rpe = $_GET['stdint-rpe'];

			$this->addTParam('rpe', $rpe);

			if($rpe > 0)
				$this->loadRescast();

			$this->rpe = $rpe;

			return true;
		}

		private function loadRescast()
		{
			$this->addComponentRequest(1, array('rpp' => 0));
		}

		private function loadPool()
		{
			$count = 10;

			if(isset($_GET['stdint-rpc']))
				$count = $_GET['stdint-rpc'];

			$this->addComponentRequest(2, array('rpp' => $this->rpp, 'rpc' => $count));
		}

		private function fillState()
		{
			// get the mode
			if(isset($_GET['stdint-rpm']))
				$this->mode = $_GET['stdint-rpm'];
			else
				$this->mode = 0;

			// get the current page
			if(isset($_GET['stdint-rpp']))
				$this->rpp = $_GET['stdint-rpp'];
			else
				$this->rpp = 1;
		}

		protected function generateFallbackLink()
		{
			if(!$this->isJSEnabled())
				return false;

			switch($this->mode) {
			case 0:
				$this->fallbackViewMode();
			break;

			case 1:
				$this->fallbackAddMode();
			break;

			case 2:
				$this->fallbackEditMode();
			break;
			}
		}

		public function applyRequest($result)
		{
			foreach($result as $rs)
				switch($rs['jack']) {
				case 1:
					$this->addTParam('resCast', $rs['result']);
				break;

				case 2:
					$this->addTParam('resPool', $rs['result']);
				break;

				case 7:
					$this->addTParam('res', $rs['result']);
				break;
				}
		}

		private function fallbackViewMode()
		{
			$qstr = QStringModifier::modifyParams(array('stdint-rpp' => ($this->rpp+1), 'stdint-rpm' => null));
			$this->addFallbackLink('next', $qstr);

			if($this->rpp > 2)
				$qstr = QStringModifier::modifyParams(array('stdint-rpp' => ($this->rpp-1), 
									    'stdint-rpm' => null,
									    'stdint-rpe' => null));
			else
				$qstr = QStringModifier::modifyParams(array('stdint-rpp' => null, 
									    'stdint-rpm' => null,
									    'stdint-rpe' => null));

			$this->addFallbackLink('prev', $qstr);

			$qstr = QStringModifier::modifyParams(array('stdint-rpm' => 1, 'stdint-rpp' => null));
			$this->addFallbackLink('addnew', $qstr);

			$qstr = QStringModifier::modifyParams(array('stdint-rpm' => 2));
			$this->addFallbackLink('modify', $qstr);
		}

		private function fallbackAddMode()
		{
			$qstr = QStringModifier::modifyParams(array('stdint-rpm' => 0));
			$this->addFallbackLink('cancel', $qstr);
			$spath = SystemConfig::appRelativePath(Managers::AppConfig()->setting('submitrequest'));

			$qstr = "$spath?cpl=#KAID#/{$this->componentId}/{$this->instanceId}/4&stdint-rpm=0";
			$this->addFallbackLink('action', $qstr);

		}

		private function fallbackEditMode()
		{
			$qstr = QStringModifier::modifyParams(array('stdint-rpm' => 0, 'stdint-rpi' => null, 'stdint-rpe' => null));
			$this->addFallbackLink('back', $qstr);

			if($this->rpe == 0) {
				$qstr = QStringModifier::modifyParams(array('stdint-rpe' => 1));
				$this->addFallbackLink('edit', $qstr);
			}
			else {
				$qstr = QStringModifier::modifyParams(array('stdint-rpe' => null));
				$this->addFallbackLink('cancel', $qstr);

				$spath = SystemConfig::appRelativePath(Managers::AppConfig()->setting('submitrequest'));
				$qstr = "$spath?cpl=#KAID#/{$this->componentId}/{$this->instanceId}/6&stdint-rpe=0";
				$this->addFallbackLink('action', $qstr);

				$spath = SystemConfig::appRelativePath(Managers::AppConfig()->setting('submitrequest'));
				$qstr = "$spath?cpl=#KAID#/{$this->componentId}/{$this->instanceId}/5&stdint-rpm=0";
				$this->addFallbackLink('remove', $qstr);
			}
		}

	}
?>
