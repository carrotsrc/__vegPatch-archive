<?php
/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	if(!defined('VP_LOADED'))
		die("vegpatch system configuration error");
	
	define("qpp_relatee", 1);
	define("qpp_part", 2);

	define("qpo_void", 0);
	define("qpo_parent", 1);
	define("qpo_child", 2);
	define("qpo_single", 3);

	class QRelatee
	{
		public $ctype = qpp_relatee;

		public $base;
		public $type;
		public $iden;
		public $xtra;

		public function generateSelect($level, $link)
		{
			echo "SELECT `rp_$level`.`id`";
			$this->generateXtra($level, $link);
			echo " FROM `respool` AS `rp_$level` ";
		}

		public function generateJoin($level, $link, $ltable)
		{
			$alias;
			if($link == qpo_parent)
				$alias = "p$level";
			else
			if($link == qpo_child)
				$alias = "c$level";
			else
			if($link == qpo_single)
				$alias = $level;

			if($link != qpo_single) {

				echo "JOIN `respool` AS `rp_$alias` ON `$ltable`.";
				if($link == qpo_parent)
					echo "`parent_id` ";
				else
					echo "`child_id` ";

				echo "=`rp_$alias`.`id` ";
			}

			echo "JOIN `rescast` AS `rc_$alias` ON `rp_$alias`.`type_id`=`rc_$alias`.`id` ";

			if($this->base != null)
				echo "JOIN `resbase` AS `rb_$alias` ON `rc_$alias`.`base`=`rb_$alias`.`id` ";
		}

		public function generateConditional($level, $link)
		{
			$sz = sizeof($this->iden);

			$alias;
			if($link == qpo_parent)
				$alias = "p$level";
			else
			if($link == qpo_child)
				$alias = "c$level";
			else
			if($link == qpo_single)
				$alias = $level;

			if($this->base != null) {
				echo "`rb_$alias`.`label`='{$this->base}' ";
				return;
			}

			echo "`rc_$alias`.`type`='{$this->type}' ";
			if($sz == 0)
				return;

			$sz -= 1;

			echo "AND (";
			foreach($this->iden as $k => $v) {
				// it's an RID
				if(is_integer($v))
					echo "`rp_$alias`.`id`='$v' ";
				else
				if(is_string($v) && is_numeric($v))
					echo "`rp_$alias`.`handler_ref`='$v' ";
				else
				if(is_string($v) && !is_numeric($v))
					echo "`rp_$alias`.`label`='$v' ";

				if($k < $sz)
					echo "OR ";
			}
			echo ") ";
		}

		private function generateXtra($level, $link)
		{
			$sz = sizeof($this->xtra);
			if($sz == 0)
				return;

			$alias = "rp_$level";

			foreach($this->xtra as $v) {
				switch($v) {
				case 'l':
				case 'label':
					echo ", `$alias`.`label`";
				break;

				case 'r':
				case 'ref':
					echo ", `$alias`.`handler_ref`";
				break;
				}
			}
		}


	}

	class QPart
	{
		public $ctype = qpp_part;
		public $out = null;

		public $parent = null;
		public $child = null;
		public $edge;

		public $pparent = null;

		public function setParent($base, $type, $iden, $xtra)
		{
			$r = new QRelatee();
			$r->base = $base;
			$r->type = $type;
			$r->iden = $iden;
			$r->xtra = $xtra;
			$this->parent = $r;

			if($this->out === null)
				$this->out = qpo_parent;
		}

		public function setParentObj($o)
		{
			$this->parent = $o;
		}

		public function setChild($base, $type, $iden, $xtra)
		{
			$r = new QRelatee();
			$r->base = $base;
			$r->type = $type;
			$r->iden = $iden;

			$r->xtra = $xtra;
			$this->child = $r;
			if($this->out === null)
				$this->out = qpo_child;
		}

		public function setChildObj($o)
		{
			$this->child = $o;
		}

		public function setEdge($e)
		{
			$this->edge = $e;
		}

		public function setParentPart(&$p)
		{
			$this->pparent = $p;
		}

		public function dump()
		{
			var_dump($this->parent);
			var_dump($this->child);
		}

		public function generateSelect($level)
		{
			if($this->out == qpo_parent) {
				$this->parent->generateSelect($level, qpo_child);
			}
			else
			if($this->out == qpo_child) {
				$this->child->generateSelect($level, qpo_parent);
			}
		}

		public function generateJoin($level, $link = null, $ltable = null)
		{
			if($this->child !== null) {
				if($link == null && $ltable == null)
					echo "JOIN `resnet` AS `net$level` ON `rp_$level`.`id` ";
				else {
					echo "JOIN `resnet` AS `net$level` ON `$ltable`.";

					if($link == qpo_parent)
						echo "`parent_id` ";
					else
					if($link == qpo_child)
						echo "`child_id` ";
				}

				echo "= `net$level`.";
				if($this->out == qpo_parent)
					echo "`parent_id`";
				else
				if($this->out == qpo_child)
					echo "`child_id`";

				if($this->edge != null)
					echo "JOIN `edgetype` AS `e_$level` ON `net$level`.`edge`=`e_$level`.`id` ";

				if($this->parent->ctype == qpp_relatee)
					$this->parent->generateJoin($level, qpo_parent, "net$level");
				else
					$this->parent->generateJoin($level+1, qpo_parent, "net$level");


				if($this->child->ctype == qpp_relatee)
					$this->child->generateJoin($level, qpo_child, "net$level");
				else
					$this->child->generateJoin($level+1, qpo_child, "net$level");
			}
			else
				$this->parent->generateJoin($level, qpo_single, null);
		}

		public function generateConditional($level, $link = null)
		{
			if($level == 0)
				echo "WHERE ";

			if($this->child !== null) {
				if($this->parent->ctype == qpp_relatee)
					$this->parent->generateConditional($level, qpo_parent);
				else
					$this->parent->generateConditional($level+1, qpo_parent);

				echo "AND ";
				if($this->child->ctype == qpp_relatee)
					$this->child->generateConditional($level, qpo_child);
				else
					$this->child->generateConditional($level+1, qpo_child);

				if($this->edge != null) {
					echo "AND `e_$level`.`label`='{$this->edge}' ";
				}
			}
			else
				$this->parent->generateConditional($level, qpo_single);
		}
	}
?>
