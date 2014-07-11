<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	require_once("channel.php");


	class ChanMan
	{
		private $db = null;
		
		public function __construct($db)
		{
			$this->db = $db;
		}

		public function getChannel($cID)
		{
			$lDb = $this->db;
			
			if($lDb == null)
				return false;

			$sql = "SELECT `channelnodes`.`pid`, `channelnodes`.`inst` FROM `channelnodes` ";
			$sql .= "LEFT JOIN `channelpool` on `channelnodes`.`channel` = `channelpool`.`id` ";
			$sql .= "WHERE `channelpool`.`id`='$cID' ";
			$sql .= "ORDER BY `channelnodes`.`seq`;";
			$result = $this->db->sendQuery($sql, false, true);
			if(!$result)
				return null;

			$channel = new Channel();
			$size = sizeof($result);
			foreach($result as $pldata)
			{

				$plugin = Managers::PluginManager()->getPlugin($pldata['pid'], $pldata['inst']);
				if(!$plugin)
					return null;

				$channel->addPlugin($plugin);
			}

			return $channel;
		}

	}
?>
