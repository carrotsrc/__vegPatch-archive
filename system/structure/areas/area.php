<?php
/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

	class Area
	{
		private $aId;
		private $aHolder;
		private $surroundId;
		private $templateId;
		private $areaName;
		
		public function __construct($id, $name, $surround, $template)
		{
			$this->aId = intval($id);
			$this->areaName = $name;
			$this->surroundId = $surround;
			$this->templateId = $template;
		}
		
		public function getSurround()
		{
			return $this->surroundId;
		}
		
		public function getName()
		{
			return $this->areaName;
		}
		
		public function getTemplate()
		{
			return $this->templateId;
		}
		
		public function setAssetHolder($holder)
		{
			$this->aHolder = $holder;
		}
		
		public function getAssetHolder()
		{
			return $this->aHolder;
		}

		public function getId()
		{
			return $this->aId;
		}
	}
