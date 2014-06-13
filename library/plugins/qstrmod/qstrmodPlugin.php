<?php
/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	class qstrmodPlugin extends Plugin
	{
		public function init($instance)
		{
			$this->instance = $instance;
		}

		public function process(&$signal)
		{
			if(Session::get('nojs') == null ||
			   Session::get('nojs') == 0)
				return $signal;

			if(!isset($signal['qstr']))
				return $signal;
		}
	}
?>
