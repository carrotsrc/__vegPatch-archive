<?php

	class RoleMan
	{
		private static $roleList = null;
		
		public static function getResId($role, $db)
		{
			if(sizeof(self::$roleList) == null)
			{
				//$query = "SELECT * FROM respool WHERE label='$role';";
				//$result = $db->sendQuery($query);
			
				//if(!$result)
					//return null;
				
				//if(mysql_num_rows($result) == 0)
					//return null;
				
				/*
				*	fill out array
				*/
				self::$roleList = array();
			}

			
			return isset(self::$roleList[$role]) ? self::$roleList[$role] : "-1";
			
		}
	}

?>
