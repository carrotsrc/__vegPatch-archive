<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	class channel extends StrapBase
	{
		private $channel = array();

		public function process(&$xml)
		{
			global $log;
			$name = "";
			$out = null;
			$rout = null;
			$cid = null;
			while(($tag = $xml->getNextTag()) != null) {
				if($tag->name == "/obj")
					break;

				if($tag->name == "channel" || $tag->name == "crudops") {
					$rtype = "Channel";
					if($tag->name == "crudops")
						$rtype = "CrudOps";

					foreach($tag->attributes as $a => $v)
						if($a == 'name')
							$name = $v;
						else
						if($a == 'out')
							$out = $v;
						else
						if($a == 'rout')
							$rout = $v;

					$this->handleChannel($xml);
					if($this->db->sendQuery("SELECT `id` FROM `channelpool` WHERE `label`='$name';")) {
						$log[] = "# Channel('$name') already exists";
						return;
					}
					$cid = $this->arrayInsert('channelpool', array('label' => $name));

					if(!$cid) {
						$log[] = "! Failed to create $rtype('$name')";
						return;
					}
					
					$cid = $this->db->getLastId();
					$log[] = "+ Created $rtype('$name')";

					foreach($this->channel as $k => $c) {
						$k++;
						if(!$this->arrayInsert('channelnodes', array(
											'seq' => $k,
											'pid' => $c[0],
											'inst' => $c[1],
											'channel' => $cid))) {
							$log[] = "!\tFailed to add Plugin('{$c[0]}') -> {$c[1]} to $rtype('$name')";
						}
						else
							$log[] = "+\tAdded Plugin('{$c[0]}') -> {$c[1]} to $rtype('$name')";

					}

					$ridc = $this->resManager->addResource($rtype, $cid, $name);
					
					if($out != null)
						setVariable($out, $cid);

					if($rout != null)
						setVariable($rout, $ridc);
					$this->channel = array();
				}
			}
		}

		private function handleChannel(&$xml)
		{
			while(($tag = $xml->getNextTag()) != null) {
				if($tag->name == "/channel" || $tag->name == "/crudops")
					break;

				if($tag->name == "plugin")
					$this->handlePlugin($tag);

			}
		}

		private function handlePlugin($tag)
		{
			$inst = $id = "0";

			foreach($tag->attributes as $a => $v)
				if($a == 'id')
					$id = processVariable($v);
				else
				if($a == 'ref')
					$inst = processVariable($v);

			$this->channel[] = array($id, $inst);
		}
	}
?>
