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
			$name = "";
			$out = null;
			$rout = null;
			$cid = null;
			while(($tag = $xml->getNextTag()) != null) {
				if($tag->element == "/obj")
					break;

				if($tag->element == "channel" || $tag->element == "crudops") {
					$rtype = "Channel";
					if($tag->element == "crudops")
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

					$cid = $this->arrayInsert('channelpool', array('label' => $name));
					if($cid != false)
						$cid = $this->db->getLastId();

					foreach($this->channel as $k => $c) {
						$k++;
						$this->arrayInsert('channelnodes', array(
											'seq' => $k,
											'pid' => $c[0],
											'inst' => $c[1],
											'channel' => $cid));
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
				if($tag->element == "/channel" || $tag->element == "/crudops")
					break;

				if($tag->element == "plugin")
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
