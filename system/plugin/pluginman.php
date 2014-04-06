<?php
/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	require_once("plugin.php");
	class PluginMan
	{
		private $db;
		private $loaded = array();

		public function __construct($db)
		{
			$this->db = $db;
		}

		public function getPlugin($id, $instance)
		{
			$label = null;
			
			$sql = "SELECT `modreg`.`module_name` FROM `modreg` WHERE `modreg`.`id`='$id';";
			$result = $this->db->sendQuery($sql, false, false);
			if(!$result)
				return false;
			$label = $result[0][0];
		//	TODO:
		//	This needs to have a running check of which plugins are loaded
		//	so we don't have to just include once

			$plPath = SystemConfig::appRootPath("library/plugins/".$label."/".$label."Plugin.php");
			include_once($plPath);

			$plClass = $label."Plugin";
			$plugin = new $plClass($this->db, $id);
			
			$plugin->init($instance);
			return $plugin;
		}
	}

?>
