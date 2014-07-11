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
			$this->includeTemplate("templates/basic.php");
		}

		public function initialize($params = null)
		{
			switch($this->crud) {
			case PNL_CM_CREATE:
				$this->mode = 1; // here we add an instance
			break;

			case PNL_CM_REMOVE:
				
			break;
			}
			echo "CRUD LOADED";
		}

		public function applyRequest($result)
		{

		}

		public function setAssets()
		{

		}
	}
?>
