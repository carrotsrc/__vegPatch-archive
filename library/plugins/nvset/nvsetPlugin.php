<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	class nvsetPlugin extends Plugin
	{
		public function init($instance)
		{
			$this->instance = $instance;
		}

		public function process(&$signal)
		{
			$area = $signal['area']->getId();
			$layout = $signal['layout']->getId();
			$inst = $signal['_pnl']->getRef();
			$cmpt = $signal['_pnl']->getComponentId();
			$nv = null;
			if(($nv = Session::get('nva')) == null)
				$nv = array();

			$width = floor((PHP_INT_SIZE<<3)/3);
			$field = $inst;
			$field <<= $width;
			$field ^= $layout;
			$field <<= $width;
			$field ^= $area;
			$width = PHP_INT_SIZE<<2;

			$ca = Session::get('aid')<<$width;
			$ca ^= 0;

			$nv[$field] = array($ca, array());
			Session::set('nvc', $nv);
			$signal['nvc'] = $field;

			return $signal;
		}
	}
?>
