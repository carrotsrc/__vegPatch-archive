<?php
/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	class vp_linkstack_uiPanel extends Panel
	{
		public function __construct()
		{
			parent::__construct();
			$this->setModuleName('vp_linkstack_ui');
		}

		public function loadTemplate()
		{
			$qstr = QStringModifier::modifyParams(array('loc' => null));
			$this->addFallbackLink('clear', $qstr);
			$this->addTParam('mediag',SystemConfig::relativeAppPath("library/media/kura/general"));
			$this->includeTemplate("template/main.php");
		}

		public function initialize($params = null)
		{
			$this->addComponentRequest(1,101);
			parent::initialize();
		}
		public function applyRequest($result)
		{

			foreach($result as $rs) {
				switch($rs['jack']) {
				case 1:
					$this->addTParam('access', $rs['result']);
				break;
				}
			}
		}

		public function setAssets()
		{
			$this->addAsset('css', "/.assets/general.css");
		}
	}
?>
