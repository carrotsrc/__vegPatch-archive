<?php
/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	class HttpHeader
	{
		public static $lockredirect = false;
		static public function fromType($type, $vars = null)
		{
			switch($type) {
			case 'css':
				self::css();
			break;

			case 'js':
				self::js();
			break;

			case 'redirect':
				self::redirect($location);
			break;

			case 'html':
			default:
				self::html();
			break;
			}
		}

		static public function css()
		{
			header('Content-Type: text/css');
		}

		static public function js()
		{
			header('Content-Type: text/javascript');
		}

		static public function html()
		{
			header('Content-Type: text/html');
		}

		static public function redirect($location)
		{
			if(!self::$lockredirect) {
				header("Location: $location");
				self::$lockredirect = true;
			}
		}

		static public function flagLock($flag)
		{
			self::$lockredirect = $flag;
		}
	}
?>
