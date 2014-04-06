<?php
/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	class toolpanelPanel extends Panel
	{
		public function __construct()
		{
			parent::__construct();
			$this->setModuleName('tpanel');
			$this->jsCommon = 'TPanelInterface';
		}

		public function loadTemplate()
		{
			$this->includeTemplate("templates/basic.php");
		}

		public function setAssets()
		{
			$this->addAsset("js", "templates/res/toolpanel.js");
		}

		public function initialize($params = null)
		{
			$this->componentId = 0;
			$this->moduleInstance = 0;
			parent::initialize($params);
		}

		public function applyRequest($result)
		{
			
		}
	}
?>
