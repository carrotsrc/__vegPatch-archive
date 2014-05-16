<?php
/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	abstract class Plugin
	{
		protected $db;
		protected $id;
		protected $instance;
		
		public function __construct($connection, $id)
		{
			$this->db = $connection;
			$this->id = $id;
		}
		
		protected final function setInstance($id)
		{
			$this->instance = $id;
		}
		
		public final function getInstance()
		{
			return $this->instance;
		}

		public final function getConfig($config)
		{
			$sql = "SELECT `widget_cfgreg`.`value` FROM `widget_cfgreg` JOIN `rescast` ON `widget_cfgreg`.`type` = `rescast`.`id` ";
			$sql .= "WHERE `rescast`.`type`='Plugin' AND `widget_cfgreg`.`inst`='{$this->instance}' ";
			$sql .= "AND `widget_cfgreg`.`cid`='{$this->id}' AND `widget_cfgreg`.`config`='$config';";
			$r = $this->db->sendQuery($sql, false, false);
			if(!$r)
				return null;

			return $r[0][0];
		}

		abstract public function process(&$params);
		abstract public function init($instance);

		public function getConfigList()
		{
			return null;
		}
	}
?>
