<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

	class Session
	{
		public static function set($var, $val)
		{
			$_SESSION[$var] = $val;
		}
		
		public static function uset($var)
		{
			unset($_SESSION[$var]);
		}
		
		public static function get($var)
		{
			if(!isset($_SESSION[$var]))
				return null;
				
			return $_SESSION[$var];
		}
		
		public static function start()
		{
			return session_start();
		}
		
		public static function wipe()
		{
			session_unset();
		}
		
		public static function destroy()
		{
			return session_destroy();
		}

		public static function seralize($var)
		{
			/*  TODO:
			*   Implement serializing of
			*   vars to sensitive session
			*   data in database
			*/
		}
	}

?>
