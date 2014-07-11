<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	if(!defined('VP_LOADED'))
		die("vegpatch system configuration error");
	
	define("qpp_item", 1);
	define("qpp_relationship", 2);

	define("qpo_void", 1);
	define("qpo_parent", 2);
	define("qpo_child", 4);
	define("qpo_single", 8);

	define("qpo_parent_child", 16);

	/*
	* these two classes map out the behaviour for
	* two different kinds of objects in a resource query:
	* - a QRelationship is a container for a parent-child relationship
	* - a QItem which is an individual relationship item; a parent or a child
	*
	* any relationship item in a QRelationship can also be a QRelationship so in effect
	* you can query branches of relationships on both sides of a statement.
	*/
	class QItem
	{
		public $ctype = qpp_item;

		public $base;
		public $type;
		public $iden;
		public $xtra;

		public function generateSelect($level, $flag)
		{
			echo "SELECT ";
			if(!$level)
				echo "DISTINCT ";
			echo "`rp_$level`.`id`";
			if($flag & qpo_parent_child) {
				$this->generateXtra($level, $flag^qpo_parent_child);
				echo ", `rpck`.`id` ";
				$this->generateXtra($level, $flag);
			}
			else
				$this->generateXtra($level, $flag);

			echo " FROM `respool` AS `rp_$level` ";
		}

		public function generateJoin($level, $flag, $ltable)
		{
			$alias;
			if($flag & qpo_parent)
				$alias = "p$level";
			else
			if($flag & qpo_child)
				$alias = "c$level";
			else
			if($flag & qpo_single)
				$alias = $level;

			if($flag != qpo_single) {

				echo "JOIN `respool` AS `rp_$alias` ON `$ltable`.";
				if($flag & qpo_parent)
					echo "`parent_id` ";
				else
					echo "`child_id` ";

				echo "=`rp_$alias`.`id` ";
			}

			echo "JOIN `rescast` AS `rc_$alias` ON `rp_$alias`.`type_id`=`rc_$alias`.`id` ";

			if($this->base != null)
				echo "JOIN `resbase` AS `rb_$alias` ON `rc_$alias`.`base`=`rb_$alias`.`id` ";

		}

		public function generateConditional($level, $flag)
		{
			$sz = sizeof($this->iden);

			$alias;
			if($flag & qpo_parent)
				$alias = "p$level";
			else
			if($flag & qpo_child)
				$alias = "c$level";
			else
			if($flag & qpo_single)
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

		private function generateXtra($level, $flag)
		{
			if(!isset($this->xtra[0]))
				return;

			$alias = "rp_$level";
			if($flag & qpo_parent_child)
				$alias = "rpck";

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

	class QRelationship
	{
		public $ctype = qpp_relationship;
		public $out = null;

		public $parent = null;
		public $child = null;
		public $edge;

		public $pparent = null;

		public function setParent($base, $type, $iden, $xtra)
		{
			$r = new QItem();
			$r->base = $base;
			$r->type = $type;
			$r->iden = $iden;
			$r->xtra = $xtra;
			$this->parent = $r;

			if(!($this->out&qpo_child) && !($this->out&qpo_parent))
				$this->out ^= qpo_parent;
		}

		public function setParentObj($o)
		{
			$this->parent = $o;
			if(!($this->out&qpo_child) && !($this->out&qpo_parent))
				$this->out ^= qpo_parent;
		}

		public function setChild($base, $type, $iden, $xtra)
		{
			$r = new QItem();
			$r->base = $base;
			$r->type = $type;
			$r->iden = $iden;

			$r->xtra = $xtra;
			$this->child = $r;
			if(!($this->out&qpo_child) && !($this->out&qpo_parent))
				$this->out ^= qpo_child;
		}

		public function setChildObj($o)
		{
			$this->child = $o;
			if(!($this->out&qpo_child) && !($this->out&qpo_parent))
				$this->out ^= qpo_child;
		}

		public function setEdge($e)
		{
			$this->edge = $e;
		}

		public function setParentPart(&$p)
		{
			$this->pparent = $p;
		}

		public function setFlag($flag)
		{
			$this->out ^= $flag;
		}

		public function generateSelect($level)
		{
			if($this->out & qpo_parent) {
				$this->parent->generateSelect($level, qpo_child^($this->out&qpo_parent_child));
			}
			else
			if($this->out & qpo_child) {
				$this->child->generateSelect($level, qpo_parent^($this->out&qpo_parent_child));
			}
		}

		public function generateJoin($level, $flag = null, $ltable = null)
		{
			if($this->child !== null) {
				$clevel = $level;

				if($this->out & qpo_parent_child)
					echo "JOIN `respool` AS `rpck` ";

				if($flag == null && $ltable == null)
					echo "JOIN `resnet` AS `net$level` ON `rp_$level`.`id` ";
				else {
					echo "JOIN `resnet` AS `net$level` ON `$ltable`.";

					if($flag & qpo_parent)
						echo "`parent_id` ";
					else
					if($flag & qpo_child)
						echo "`child_id` ";

				}

				echo "= `net$level`.";
				if($this->out & qpo_parent)
					echo "`parent_id` ";
				else
				if($this->out & qpo_child)
					echo "`child_id` ";

				if($this->out & qpo_parent_child) {
					/* it will always be on net0 because that is
					*  the relationship that is requested from
					*  the network
					*/
					echo "AND `rpck`.`id` = `net0`.";
					if($flag & qpo_parent)
						echo "`child_id` ";
					else
					if($flag & qpo_child)
						echo "`parent_id` ";
				}

				if($this->edge != null)
					echo "JOIN `edgetype` AS `e_$level` ON `net$level`.`edge`=`e_$level`.`id` ";

				if($this->parent->ctype == qpp_item)
					$this->parent->generateJoin($clevel, qpo_parent^($this->out&qpo_parent_child), "net$clevel");
				else {
					$this->parent->generateJoin($level+1, qpo_parent, "net$clevel");
					if($level == 0)
						$level = 64;
				}


				if($this->child->ctype == qpp_item)
					$this->child->generateJoin($clevel, qpo_child^($this->out&qpo_parent_child), "net$clevel");
				else
					$this->child->generateJoin($level+1, qpo_child, "net$clevel");
			}
			else
				$this->parent->generateJoin($level, qpo_single, null);
		}

		public function generateConditional($level, $flag = null)
		{
			if($level == 0)
				echo "WHERE ";

			if($this->child !== null) {
				$clevel = $level;
				if($this->parent->ctype == qpp_item)
					$this->parent->generateConditional($clevel, qpo_parent);
				else {
					$this->parent->generateConditional($level+1, qpo_parent);
					if($level == 0)
						$level = 64;
				}

				echo "AND ";
				if($this->child->ctype == qpp_item)
					$this->child->generateConditional($clevel, qpo_child);
				else
					$this->child->generateConditional($level+1, qpo_child);

				if($this->edge != null) {
					echo "AND `e_$clevel`.`label`='{$this->edge}' ";
				}
			}
			else
				$this->parent->generateConditional($level, qpo_single);
		}
	}
?>
