<?php
/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

	/*
	*	Abstract component class
	*/
	include_once("jackinterface.php");

	/*
	*  There should be a check to see that the instance
	*  requested is in the system
	*/
	define('RIO_INS', 1);
	define('RIO_SEL', 2);
	define('RIO_UPD', 3);
	define('RIO_DEL', 4);
	abstract class Component extends DBAcc implements IModArg
	{
		protected $instanceId;
		protected $componentName;
		protected $componentId;
		protected $rioType;
		protected $rioId;

		public function __construct($id, $cid = null)
		{
			$this->instanceId = $id;
			$this->rioType = null;
			$this->rioId = null;
			$this->componentId = $cid;
		}

		public function createInstance($params = null)
		{
			if($this->instanceId > 0)
				return false;

			// by default if no instance is need
			// it will set it as 0 for the handler
			return 0;
		}

		public function getRio()
		{
			return array($this->rioType, $this->rioId);
		}

		protected function setRio($type, $rid)
		{
			$this->rioType = $type;
			$this->rioId = $rid;
		}

		public function removeInstance($id)
		{
			if($this->instanceId > 0)
				return false;

			return 0;
		}

		/* this is used in maintence mode to get
		*  a particular resource.
		*  ideally it should not be done in
		*  maintainence mode but for now it will
		*  have to be in order to use floating
		*  panels properly
		*/
		public function getResourceFromHandle($hid)
		{
			if($this->instanceId > 0)
				return null;

			return $this->resourceFromHandle($hid);
		}

		abstract public function initialize();
		abstract public function run($jack = null, $args = null);

		/*
		*  Actual functionality for getting a resource
		*  from the ID.
		*/
		protected function resourceFromHandle($hid)
		{
			return null;
		}

		final public function argVar($vars, $args = null)
		{
			if(!is_array($vars))
				return null;

			$r = new CBlank();
			foreach($vars as $key => $value) {
				$v = null;
				if($args == null && isset($_GET[$key]))
					$v = $_GET[$key];
				else
				if(isset($args[$key]))
					$v = $args[$key];

				if($v == null)
					continue;

				$r->__set($value, $v);
			}

			return $r;
		}

		final protected function initReady()
		{
			// check to see if the component is not in
			// maintenance mode
			if($this->instanceId < 1 || $this->instanceId === null)
				return false;

			return true;
		}

		final protected function maintainReady()
		{
			// check to see if the component is
			// set for maintainence
			if($this->instanceId > 0)
				return false;

			return true;

			// if a component has an instance id of 0
			// it is in maintence mode and normal jack
			// interfaces are inaccessable

			// it requires a valid instance
		}

		final protected function addTrackerParam($param, $value)
		{
			// this is useful for dealing with component return
			$track = Session::get('track');
			if($value == null)
				unset($track[1][$param]);
			else
				$track[1][$param] = $value;

			Session::set('track', $track);

		}

		protected final function getConfig($config)
		{
			$sql = "SELECT `widget_cfgreg`.`value` FROM `widget_cfgreg` JOIN `rescast` ON `widget_cfgreg`.`type` = `rescast`.`id` ";
			$sql .= "WHERE `rescast`.`type`='Component' AND `widget_cfgreg`.`inst`='{$this->instanceId}' ";
			$sql .= "AND `widget_cfgreg`.`cid`='{$this->componentId}' AND `widget_cfgreg`.`config`='$config';";
			$r = $this->db->sendQuery($sql, false, false);
			if(!$r)
				return null;

			return $r[0][0];
		}
		
		public function getConfigList()
		{
			return null;
		}
	}
?>
