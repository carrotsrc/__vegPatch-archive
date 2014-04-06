<?php
/* (C)opyright 2014, Carrotsrc.org
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

		public function process(&$params)
		{
			$area = $params['area']->getId();
			$layout = $params['layout']->getId();
			$inst = $params['_pnl']->getRef();
			$cmpt = $params['_pnl']->getComponentId();
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
			$params['nvc'] = $field;

			return $params;
		}
	}
?>
