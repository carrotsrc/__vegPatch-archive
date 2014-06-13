<?php
/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

	abstract class DBAcc
	{
		protected $db;
		

		public final function setDatabase($dbObj)
		{
			$this->db = $dbObj;
		}
		
		public function __construct()
		{
			$this->db = null;
		}


		protected function arrayToString($a)
		{
			$str = "";
			// get ref cols
			foreach($a as $row) {
				if(is_array($row))
					foreach($row as $col)
						$str .= $col.",";
				else
					$str .= $row;
				$str .= "\n";
			}
			return $str;
		}

		protected function arrayInsert($table, $vals)
		{
			$sz = sizeof($vals);
			$sql = "INSERT INTO $table (";
			$sv = " VALUES (";
			foreach($vals as $k => $v) {
				$sql .="`$k`";
				$v =  mysql_real_escape_string($v);
				$sv .= "'$v'";
				if($sz-- == 1)
					continue;

				$sql .= ", ";
				$sv .= ", ";
			}

			$sql .=")".$sv.");";
			return $this->db->sendQuery($sql, false, false);
		}

		protected function arrayUpdate($table, $vals, $where)
		{
			if($where == null)
				return false;

			$sz = sizeof($vals);
			$sql = "UPDATE $table SET ";
			foreach($vals as $k => $v) {
				if($v === null) {
					$sz--;
					continue;
				}

				$sql .="`$k`='$v'";
				if($sz-- == 1)
					continue;

				$sql .= ", ";
			}
			$sql .=" WHERE ".$where.";";

			return $this->db->sendQuery($sql);
		}
	}
?>
