<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	class Channel
	{
		protected $pluginStack;
		
		public function __construct()
		{
			$this->pluginStack = array();
		}
		
		public function addPlugin($plugin)
		{
			$this->pluginStack[] = $plugin;
		}
		
		public function runSignal(&$signal)
		{
			$msgBox = array();
			foreach($this->pluginStack as $plugin)
				if(($signal = $plugin->process($signal)) === false)
					return false;
			
			return $signal;
		}
	}

	function core_get_channel($id, $db)
	{
		if($db == null)
			return false;

		$sql = "SELECT `channelnodes`.`pid`, `channelnodes`.`inst` FROM `channelnodes` ";
		$sql .= "LEFT JOIN `channelpool` on `channelnodes`.`channel` = `channelpool`.`id` ";
		$sql .= "WHERE `channelpool`.`id`='$cID' ";
		$sql .= "ORDER BY `channelnodes`.`seq`;";
		$result = $db->sendQuery($sql);
		if(!$result)
			return null;

		$channel = new Channel();
		$size = sizeof($result);
		foreach($result as $pldata)
		{

			$plugin = ModMan::getPlugin($pldata['pid'], $pldata['inst'], $db);
			if(!$plugin)
				return null;

			$channel->addPlugin($plugin);
		}

		return $channel;
	}
?>
