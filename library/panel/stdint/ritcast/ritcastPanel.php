<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	class ritcastPanel extends Panel
	{
		private $mode;
		public function __construct()
		{
			parent::__construct();
			$this->setModuleName('ritcast');
			$this->jsCommon = 'RitcastInterface';
			$this->mode = 0;
		}

		public function loadTemplate()
		{
			switch($this->mode) {
			case 0:
				$this->includeTemplate("templates/basic.php");
			break;
			}
		}

		public function setAssets()
		{
			$this->addAsset("js", "/share/riglob.js");
			$this->addAsset("js", "templates/res/ritcast.js");
		}

		public function initialize($params = null)
		{
			$this->resourceManager = Managers::ResourceManager();
			$this->loadResourceCast();
			parent::initialize($params);
		}

		private function loadResourceCast()
		{
			$this->addComponentRequest(1, array('void'));
		}

		private function loadEdit()
		{

		}

		public function applyRequest($result)
		{
			foreach($result as $rq)
				switch($rq['jack']) {
				case 1:
					$this->addTParam('resCast', $rq['result']); 
				break;
				}
		}

		private function fillState()
		{
			if(isset($_GET['stdint-rcm']))
				$this->mode = $_GET['stdint-rcm'];
		}
	}
?>
