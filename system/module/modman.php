<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

	include_once("imodarg.php");
	include_once("panel.php");
	include_once("component.php");
	include_once("plugin.php");
	/*
	*	ModMan
	*
	*	Used for working with modules:
	*	Components: The working part of a module
	*	Panel:	The front ends for a given component
	*/
	class ModMan
	{
		private static $id = 0;

		static function getPanel($panel, $db = null)
		{
			$cid = null;
			$space = null;
			if(is_numeric($panel))
			{
				$cid = $panel;
				$query = "SELECT * FROM modreg WHERE id = '$panel';";
				$result = $db->sendQuery($query);
				if(!$result)
					return null;
				
				if(mysql_num_rows($result) == 0)
					return null;

				$row = mysql_fetch_assoc($result);
				if($row['active'] == 0)
					return null;

				$panel = $row['module_name'];
				$space = $row['space'];
			}
			else
			{
				$query = "SELECT * FROM modreg WHERE module_name = '$panel';";
				$result = $db->sendQuery($query);
				
				if(!$result)
					return null;
				
				if(mysql_num_rows($result) == 0)
					return null;

				$row = mysql_fetch_assoc($result);
				
				if($row['active'] == 0)
					return null;

				$cid = $row['id'];
				$space = $row['space'];
			}
			
			//	Get absolute path from SystemConfig
			$inc = SystemConfig::libraryPath() . "panel/$space/$panel/{$panel}Panel.php";
			$absolute =  "panel/$space/$panel/";
			$relative =  "panel/$space/";
			if(!file_exists($inc))
				return null;
			include_once($inc);
			$class = $panel."Panel";
			$obj = new $class();

			/* TODO
			*  Sort this crap out. Properties
			*  (and methods) are confusingly name
			*/
			$obj->setModuleId($cid);
			$obj->setModuleSpace($space);
			$obj->setId(self::$id);

			$obj->setAbsolutePath($absolute);
			$obj->setLocalRelative($relative);

			self::$id++; // Danger, Will Robinson!
			return $obj;
		}

		static function getComponent($component, $instance, $db = null)
		{
			$space = null;
			if(is_numeric($component))
			{
				$sql = "SELECT * FROM modreg WHERE id='$component' AND module_type='0';";

				$result = $db->sendQuery($sql, false, true);
				if(!$result)
					return null;

				$row = $result[0];

				if($row['active'] == 0)
					return null;

				$component = $row['module_name'];
				$space = $row['space'];
			}
			else {
				$sql = "SELECT * FROM modreg WHERE module_name='$component' AND module_type='0';";

				$result = $db->sendQuery($sql, false, true);
				if(!$result)
					return null;

				$row = $result[0];

				if($row['active'] == 0)
					return null;

				$component = $row['module_name'];
				$space = $row['space'];
			}

			$inc = SystemConfig::libraryPath() . "components/$space/$component/{$component}Component.php";
			if(!file_exists($inc))
				return null;

			include_once($inc);
			$class = $component."Component";
			$obj = new $class($instance, $row['id']);
			$obj->setDatabase($db);
			self::$id++;
			return $obj;
		}

		static function getPlugin($plugin, $instance, $db = null)
		{
			$label = null;
			
			$sql = "SELECT `modreg`.`module_name` FROM `modreg` WHERE `modreg`.`id`='$plugin';";
			$result = $db->sendQuery($sql);
			if(!$result)
				return false;
			$label = $result[0]['module_name'];

			$plPath = SystemConfig::appRootPath("library/plugins/".$label."/".$label."Plugin.php");
			include_once($plPath);

			$plClass = $label."Plugin";
			$plugin = new $plClass($db, $plugin);
			
			$plugin->init($instance);
			return $plugin;
		}

		static function getInterface($id, $jack = null, $db = null)
		{
			$sql = "SELECT interface, jack FROM interfacenodes";

			if(is_array($id)) {
				$sql .= " WHERE interface='{$id[0]}'";
				$nInt = sizeof($id);
				for($i = 1; $i < $nInt; $i++)
					$sql .= " OR interface='{$id[$i]}'";
			}
			else
			if(is_numeric($id))
				$sql .= " WHERE interface='$id'";

			if($jack != null)
				$sql .= " AND jack='$jack'";

			$sql .= ";";

			$result = $db->sendQuery($sql, false, false);
			if(!$result)
				return null;

			$jls = array();
			$cid = $result[0][0];

			foreach($result as $int)
				if($cid != $int[0])
					return null;
				else
					$jls[] = $int[1];

			return new JackInterface($cid, $jls);
		}
	}
	

?>
