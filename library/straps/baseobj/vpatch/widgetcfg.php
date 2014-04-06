<?php
/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	class widgetcfg extends StrapBase
	{
		public function process(&$xml)
		{
			while(($tag = $xml->getNextTag()) != null) {
				if($tag->element == "/obj")
					break;

				if($tag->element == "config")
					$this->handleConfig($tag);
			}
		}

		private function handleConfig($tag)
		{
			$type = null;
			$name = null;
			$value = null;
			$cid = null;
			$inst = null;
			foreach($tag->attributes as $a => $v) {
				switch($a) {
				case 'type':
					$r = $this->db->sendQuery("SELECT id FROM rescast WHERE type='$v';", false, false);
					if($r)
						$type = $r[0][0];
					else
						return;
				break;

				case 'name':
					$name = $v;
				break;

				case 'value':
					$value = $v;
				break;

				case 'cid':
					$cid = processVariable($v);
				break;

				case 'inst':
					$inst = processVariable($v);
				break;
				}
			}

			$sql = "INSERT INTO `widget_cfgreg` ";
			$sql .= "(`type`,  `cid`, `inst`, `config`, `value`) VALUES ";
			$sql .= "('$type', '$cid', '$inst', '$name', '$value');";

			$this->db->sendQuery($sql, false, false);			
		}
	}
?>
