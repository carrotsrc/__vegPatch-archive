<?php
	
/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	class Log
	{
		public static $log = array();
		public static $debug = false;
		
		public static function logit($class, $msg, $type)
		{
			if(!Log::$debug)
				return;
				
			Log::$log[] = array('class' => $class,
							   'msg' => $msg,
							   'type' => $type); 
		}
		
		public static function printLog()
		{
			if(!Log::$debug)
				return;

			$local = Log::$log;
			foreach($local as $item)
			{
				switch($item['type'])
				{
				case 0:
					echo "ERR @ ";
					break;
					
				case 1:
					echo "WRN @ ";
					break;
				
				case 2:
					echo "DBG @ ";
					break;
				
				default:
					echo "UKN @ ";
				}
						
				echo $item['class'] . ": " . $item['msg'] . "<br />";
			}
		}
		
		public static function switchDebug($flag)
		{
			Log::$debug = true;
		}
		
	}
?>
