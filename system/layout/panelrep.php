<?php
/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	/*
	*  This is a representation of a panel
	*  that the system uses
	*/
	class PanelRep
	{
		private $pId;
		private $cId;
		private $ref;
		private $grp;
		private $style;
		private $vars;
		private $absRef;

		private $panel;


		public function __construct()
		{
			$this->panel = null;
			$this->style = null;
			$this->vars = null;
		}

		public function setRef($reference, $absolute = true)
		{
			/* Is it an absolute or
			*  is it a query
			*/
			$this->absRef = $absolute;
			$this->ref = $reference;
		}

		public function getRef()
		{
			return $this->ref;
		}

		public function setComponentId($id)
		{
			$this->cId = $id;
		}

		public function getComponentId()
		{
			return $this->cId;
		}

		public function setPanelId($id)
		{
			$this->pId = $id;
		}

		public function getInstanceId()
		{
			return $this->panel->getInstanceId();
		}

		public function getPanelId()
		{
			return $this->pId;
		}

		public function setGroup($id)
		{
			$this->grp = $id;
		}

		public function getGroup()
		{
			return $this->grp;
		}

		public function isAbsolute()
		{
			return $this->absRef;
		}

		public function setStyle($style)
		{
			$this->style = $style;
		}

		public function getStyle()
		{
			return $this->style;
		}

		public function setPanel($nPanel)
		{
			$nPanel->setComponentId($this->cId);
			$nPanel->setInstanceId($this->ref);
			$nPanel->setCommonGroup($this->grp);
			if($this->vars != null) {
				$nPanel->setLoadVars($this->vars);
			}
			$this->panel = $nPanel;
		}
		
		public function generateHTML()
		{
			if($this->panel == null)
				return "";
			$this->panel->loadTemplate();
			return $this->panel->getTemplate();
		}

		public function getLoadComInt()
		{
			if($this->panel == null)
				return "";

			return $this->panel->jsOnLoad();
		}

		public function getComponentRequests()
		{
			return $this->panel->componentRequests();
		}

		public function applyRequest($result)
		{
			$this->panel->applyRequest($result);
		}

		public function addGlobalParam($param, $value)
		{
			if($this->panel == null)
				return;

			$this->panel->addGLobalParam($param, $value);
		}

		public function setLoadVars($lvars)
		{
			$this->vars = $lvars;
		}
	}
?>
