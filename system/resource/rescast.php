<?php

/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

	class ResCast
	{
		private static $rescast = null;
		private static $restypes = null;
		
		public static function cast($res)
		{
			$tArray = null;
			if(self::$rescast == null)
				return null;
			else
				$tArray = self::$rescast;
				
			
			if(is_numeric($res))
			{
				foreach($tArray as $item)
					if($item['id'] == $res)
						return $item;
			}
			else
			{
				foreach($tArray as $item)
					if($item['type'] == $res)
						return $item;
			}				
			return null;
		}
		
		public static function castType($base)
		{
			
		}
		
		public static function getAllTypes()
		{
			return self::$rescast;
		}
		public static function init($db)
		{
			$result = $db->sendQuery("SELECT * FROM rescast;");
			
			if(!$result)
				return false;
			
			$tArray = array();
			while(($row = mysql_fetch_assoc($result)))
			{
				$tArray[] = array('id' => $row['id'],
								  'type'=> $row['type'],
								  'handler'=>$row['handler'],
								  'base'=> $row['base']);
			}
			
			self::$rescast = $tArray;
			
			return true;
		}
	}
?>
