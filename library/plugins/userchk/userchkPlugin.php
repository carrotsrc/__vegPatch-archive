<?php
/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	class userchkPlugin extends Plugin
	{
		public function init($instance)
		{
			$this->instance = $instance;
		}

		public function process(&$signal)
		{
			$userid = Session::get('uid');
			if($userid == null)
				$userid = 0;

				$signal['uid'] = $userid;
			return $signal;
		}

	
?>
