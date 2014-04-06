<?php
/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

	class Page extends DBAcc
	{
		private $template;
		private $layout;
		private $schema;
		private $area;

		private $params;
		
		public function __construct()
		{
			$this->template = new TemplateHolder();
			$this->area = null;
			$this->layout = null;
			$this->schema = array();
			$this->params = null;
		}

		public function renderPage()
		{	
			$this->loadSurround();
			$html = "";
			$html = $this->template->getTemplate();
			echo $html;
		}

		public function setArea($areaObj)
		{
			$this->area = $areaObj;
		}

		public function addFValue($name, $value)
		{
			$this->schema[$name] = $value;
		}

		public function setLayoutParams($params)
		{
			$this->params = $params;
		}

		private function loadSurround()
		{
			$surroundId = $this->area->getSurround();
			$templateId = $this->area->getTemplate();

			$this->area->setAssetHolder(SurroundMan::getAssetList($surroundId, $this->db));

			$vars = $this->params;

			$this->template = SurroundMan::includeTemplate($surroundId, $templateId, $vars, $this->db);

			if($this->template == null)
				Log::logit("Page", "Failed to load template surround", 0);
		}
	}
?>
