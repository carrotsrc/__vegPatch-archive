<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	if(!defined('VP_LOADED'))
		die("vegpatch system configuration error");

	include("qpart.php");

	/*
	*  Class: QParse
	*  Initial: 2013-12-04
	*  Author: cfg
	*  Updated: 2013-12-18
	*  Mod: cfg
	*
	*  Version: 0.1
	*  VegPatch: 0.2
	*
	*  Parse query lines for resources
	*/
	define('qp_block', 0);

	define('qp_iden', 1);
	define('qp_xtra', 2);
	define('qp_base', 3);
	define('qp_edge', 4);
	define('qp_rela', 5);

	define('qp_strn', 6);
	define('qp_subq', 7);

	define('qp_stat', 8);

	/*
	* TODO:
	* Need to implement subqueries on first relation
	* instead of just on second. This means before
	* shifting intp qp_rela state i.e. < and > symbols
	*/

	class QParse
	{

		public function parse($rql)
		{
			$sz = strlen($rql);
			$state = array();

			$str = "";
			$type = null;
			$base = null;
			$iden = array();
			$xtra = array();

			$cpart = null;
			$rpart = null;

			$this->testPush($state, qp_block);

			for($i = 0; $i < $sz; $i++) {
				$ch = $rql[$i];
				$end = end($state);

				switch($ch) {
				case '(':
					if($end == qp_block || $end == qp_rela) {
						$this->testPush($state, qp_subq);
						$this->testPush($state, qp_block);
						$tmp = new QPart();
						$tmp->pparent = $cpart;
						$cpart = $tmp;
					}
					else
					if($end == qp_stat) {
						$this->testPush($state, qp_iden);
						$type = $str;
					}

					$str = "";
				break;

				case '[':
					$this->testPush($state, qp_base);
				break;

				case '{':
					$this->testPush($state, qp_xtra);
				break;

				case ':':
					$this->testPush($state, qp_edge);
				break;

				case '<':
				case '>':
					
					if($cpart == null)
						$cpart = new QPart();

					$this->testPush($state, qp_rela);

					if($ch == '<')
						$cpart->setChild($base, $type, $iden, $xtra);
					else
					if($ch == ">")
						$cpart->setParent($base, $type, $iden, $xtra);

					$base = $type = null;
					$iden = array();
					$xtra = array();
				break;

				case ')':

					if($end == qp_iden && $str != "") {
						// so we have the correct differentiation
						// between string and numeric identities
						if(is_numeric($str)) {
							$str = intval($str);
							$iden[] = $str;
						}
					}
					
					$this->testPop($state);
					if(end($state) == qp_stat)
						$this->testPop($state);

					if(end($state) == qp_rela) {
						$this->testPop($state);

						if($cpart->child == null)
							$cpart->setChild($base, $type, $iden, $xtra);
						else
							$cpart->setParent($base, $type, $iden, $xtra);
					}

					if(end($state) == qp_subq) {
						// we are closing a subquery
						$this->testPop($state);
						// should be qp_rela anyway but no harm in checking
						if(end($state) == qp_rela) {
							$this->testPop($state);
							$tmp = $cpart;
							$cpart = $tmp->pparent;
							$tmp->pparent = null;

							if($cpart->child == null)
								$cpart->setChildObj($tmp);
							else
								$cpart->setParentObj($tmp);
						}
					}

					$str = "";
				break;
				
				case '}':
					if($end == qp_xtra && $str != "")
						$xtra[] = $str;

					$this->testPop($state);
					$str = "";
				break;
				
				case ']':
					$this->testPop($state);
					if($end == qp_base) {
						$base = $str;
					}
					$str = "";
				break;

				case '\'':
					if($end == qp_strn) {
						$this->testPop($state);
						$end = end($state);

						if($end == qp_iden)
							$iden[] = $str;

						$str = "";
					}
					else
						$this->testPush($state, qp_strn);
				break;

				case ',':

					if($end == qp_iden && $str != "") {
						if(is_numeric($str)) {
							$str = intval($str);
							$iden[] = $str;
						}
						$str = "";
					}
					else
					if($end == qp_xtra && $str != "") {
						$xtra[] = $str;
						$str = "";
					}

				break;

				case ' ':
					if($end == qp_strn)
						$str += " ";
				break;

				case ';':
					if($cpart == null)
						$cpart = new QPart();

					if($end == qp_edge && $str != "") {
						$edge = $str;
						$this->testPop($state);
						$cpart->setEdge($edge);
					}
					else
					if($cpart->parent == null && $cpart->child == null)
						// this is what happens when it's a single query
						$cpart->setParent($base, $type, $iden, $xtra);

					$this->testPop($state);
					$str = "";
				break;

				default:
					if($end == qp_block || $end == qp_rela)
						$this->testPush($state, qp_stat);

					$str .= $ch;
				break;
				}
			}

			return $cpart;
		}

		private function testPush(&$a, $s)
		{
			array_push($a, $s);
			//var_dump($a);
		}

		private function testPop(&$a)
		{
			array_pop($a);
			//var_dump($a);
		}

		public function generate($qpart)
		{
			ob_start();
			$qpart->generateSelect(0);
			$qpart->generateJoin(0);
			$qpart->generateConditional(0);
			$sql = ob_get_contents();
			ob_end_clean();

			return $sql;
		}

	}
?>
