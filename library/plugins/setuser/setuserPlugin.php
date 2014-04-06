<?php
/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	class setuserPlugin extends Plugin
	{
		public function init($instance)
		{
			$this->setInstance($instance);
		}

		public function process(&$params)
		{
			$params['kuid'] = 0;
			$uid = Session::get('kuid');
			// get the user from the session
			if($uid != null)
				$params['kuid'] = $uid;

			return $params;
		}
	}
?>
