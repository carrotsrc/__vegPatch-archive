<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	class bblogPanel extends Panel
	{
		private $mode;
		public function __construct()
		{
			parent::__construct();
			$this->setModuleName('bblog');
			$this->jsCommon = "BBlogInterface";
			$this->mode = 0;
		}

		public function loadTemplate()
		{
			switch($this->mode) {
			case 1:
				$this->includeTemplate("templates/newlayout.php");
			break;
			}
		}

		public function initialize($params = null)
		{
			$vars = $this->argVar(array( '_cmr' => 'area',
						'chret' => 'rcode', // component return code
						'sichk' => 'killsig',), // kill signal for panel in CRUD
						$params);

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
				break;
				}
			}
		}

		public function setAssets()
		{
			$this->addAsset("js", "/G/toolset.js");
			$this->addAsset("js", "templates/res/wireframe.js");
			$this->addAsset("js", "templates/res/kxml.js");
			$this->addAsset("css", "templates/res/wireframe.css");
			$this->addAsset("js", "templates/res/layouthndl.js");
		}

		public function addMode($vars)
		{
		}

		public function fallbackAddMode()
		{
		}

		public function previewTemplate()
		{
			$this->includeTemplate("templates/preview.htm");
			return $this->getTemplate();
		}
	}
?>
