<?php
/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	class wireframecfg extends StrapBase
	{
		public function process(&$xml)
		{
			while(($tag = $xml->getNextTag()) != null) {
				if($tag->name == "/obj")
					break;

				if($tag->name == "layout")
					$this->handleWireframe($xml, $tag);
			}
		}

		private function handleWireframe(&$xml, $tag)
		{
			global $log;
			$wireframe = "";
			if(!isset($tag->attributes['name'])) {
				$log[] = "! Failed to create Layout- name unspecified";
				return;
			}

			$name = $tag->attributes['name'];
			$out = null;
			$rout = null;
			if(isset($tag->attributes['out']))
				$out = $tag->attributes['out'];
			if(isset($tag->attributes['rout']))
				$rout = $tag->attributes['rout'];
	
			$sql = "";


			while(($tag = $xml->getNextTag()) != null) {
				if($tag->name == "_comment_")
					continue;

				if($tag->name == "/layout")
					break;

				if($tag->name == "_text_") {
					$wireframe .= $tag->attributes['content'];
					continue;
				}

				$wireframe .= "<{$tag->name}";
				foreach($tag->attributes as $a => $v) {
					$v = processVariable($v);
					$wireframe .= " $a=\"$v\"";
				}
				if($tag->name == "leaf")
					$wireframe .= " /";
				$wireframe .= ">\n";
			}

			$sql = "INSERT INTO `layoutpool` (`name`, `cml`) VALUES ('$name', '$wireframe');";
			if(!$this->db->sendQuery($sql, false, false)) {
				$log[] = "! Failed to create layout $name";
				return;
			}
			$id = $this->db->getLastId();
			$rid = $this->resManager->addResource("Layout", $id, $name);
			$log[] = "+ Added Layout('$name') => $id";
			if($out != null)
				setVariable($out, $id);

			if($rout != null)
				setVariable($rout, $rid);
		}
	}
?>
