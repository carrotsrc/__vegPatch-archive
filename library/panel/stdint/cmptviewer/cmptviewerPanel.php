<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	class cmptviewerPanel extends Panel
	{
		public function __construct()
		{
			parent::__construct();
			$this->setModuleName('cmptviewer');
		}

		public function loadTemplate()
		{
			$this->includeTemplate("templates/basic.php");
		}
	}
?>
