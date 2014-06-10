<?php
/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	class db extends StrapBase
	{
		public function process(&$xml)
		{
			while(($tag = $xml->getNextTag()) != null) {
				if($tag->element == "/obj")
					break;

				if($tag->element == "insert")
					$this->handleInsert($tag, $xml);
			}
		}

		private function handleInsert($tag, &$xml)
		{
			global $log;
			$table = null;
			$cols = array();
			foreach($tag->attributes as $a => $v)
				if($a == 'table')
					$table = $v;

			while(($tag = $xml->getNextTag()) != null) {
				if($tag->element == "/insert")
					break;

				if($tag->element == "col") {
					$cn = null;
					$cv = null;
					foreach($tag->attributes as $a => $v) {
						if($a == "name")
							$cn = $v;
						else
						if($a == "value")
							$cv = $v;
					}

					if($cv != null && $cn != null)
						$cols[$cn] = $cv;

				}
			}

			$sql = "INSERT INTO `$table` ";
			$c = "";
			$v = "";
			$sz = sizeof($cols);

			foreach($cols as $col => $val) {
				$c .= "`$col`";
				$v .= "'$val'";
				if($sz-- >1) {
					$c .=",";
					$v .=",";
				}
			}

			$sql .= "($c) VALUES ($v);";
			if(!$this->db->sendQuery($sql, false, false)) {
				$log[] = "! Database INSERT failed";
				$log[] = "$sql";
				return;
			}

			$log[] = "+ Performed INSERT successfully";
		}
	}
?>
