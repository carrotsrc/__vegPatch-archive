<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	/*
	*  This plugin sorts out a parameter
	*  for floating layouts so they have
	*  an anchor point (like an area)
	*/
	class fltanchPlugin extends Plugin 
	{
		public function init($instance)
		{
			$this->instance = $instance;
		}

		public function process(&$params)
		{
			if(Session::get('aid') == null)
				return $params;

			$params['context'] = Session::get('aid');
			return $params;
		}
}
?>
