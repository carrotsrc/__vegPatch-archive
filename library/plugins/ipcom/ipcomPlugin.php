<?php
/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	/*
	*  Interpanel Communication
	*  this may be need in the future
	*  but needs more than a potential
	*  metapanel for it to be implemented
	*/
	class ipcomPlugin extends Plugin
	{
		public function init($instance)
		{
			$this->instance = $instance;
		}

		public function process(&$params)
		{
			$wireframe = $params['layout'];
		}
	}
?>
