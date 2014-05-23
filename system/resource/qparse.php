<?php
/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	if(!defined('VP_LOADED'))
		die("vegpatch system configuration error");

	include("qpart.php");

	define('qp_block', 0);

	define('qp_iden', 1);
	define('qp_xtra', 2);
	define('qp_base', 3);
	define('qp_edge', 4);
	define('qp_rela', 5);

	define('qp_strn', 6);
	define('qp_subq', 7);

	define('qp_stat', 8);
	define('qp_wait', 9);
	global $stuff;
		$stuff = array(
			"block",
			"iden",
			"xtra",
			"base",
			"edge",
			"rela",
			"strn",
			"subq",
			"stat",
			"wait");

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
			$state = array();
			$sindex = 0;

			$str = "";
			$type = null;
			$base = null;
			$iden = array();
			$xtra = array();

			$cpart = null;
			$rpart = null;

			$this->testPush($state, qp_block);

			for($i = 0; isset($rql[$i]); $i++) {
				$ch = $rql[$i];
				$end = $state[$sindex];

				switch($ch) {
				case '(':
					if($end == qp_block || $end == qp_rela) {
						$this->testPush($state, qp_subq);
						$this->testPush($state, qp_block);
						$sindex += 2;
						$tmp = new QPart();
						$tmp->pparent = $cpart;
						$cpart = $tmp;
					}
					else
					if($end == qp_stat) {
						$this->testPush($state, qp_iden);
						$sindex++;
						$type = $str;
					}

					$str = "";
					break;

				case '[':
					$this->testPush($state, qp_base);
					$sindex++;
					break;

				case '{':
					$this->testPush($state, qp_xtra);
					$sindex++;
					break;

				case ':':
					$this->testPush($state, qp_edge);
					$sindex++;
					break;

				case '<':
				case '>':
					
					if($end == qp_wait) {
						$tmp = $cpart;
						$cpart = new QPart();
						if($ch == '<')
							$cpart->setChildObj($tmp);
						else
							$cpart->setParentObj($tmp);

						$this->testPop($state);
						$this->testPush($state, qp_rela);
						break;
					}

					if($cpart == null)
						$cpart = new QPart();

					$this->testPush($state, qp_rela);
					$sindex++;
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
					$sindex--;

					if($state[$sindex] == qp_stat) {
						$this->testPop($state);
						$sindex--;
					}

					if($state[$sindex] == qp_rela) {
						$this->testPop($state);
						$sindex--;

						if($cpart->child == null)
							$cpart->setChild($base, $type, $iden, $xtra);
						else
							$cpart->setParent($base, $type, $iden, $xtra);
					}

					if($state[$sindex] == qp_subq) {
						// we are closing a subquery
						$this->testPop($state);
						$sindex--;

						if($state[$sindex] == qp_rela) {
							// if it is in the second part of a relationship
							$this->testPop($state);
							$sindex--;

							$tmp = $cpart;
							$cpart = $tmp->pparent;
							$tmp->pparent = null;

							if($cpart->child == null)
								$cpart->setChildObj($tmp);
							else
								$cpart->setParentObj($tmp);
						}
						else {
							// we are in the first part of a relationship
							$this->testPush($state, qp_wait);
							$sindex++;
						}
					}

					$str = "";
					break;
				
				case '}':
					if($end == qp_xtra && $str != "")
						$xtra[] = $str;

					$this->testPop($state);
					$sindex--;
					$str = "";
				break;
				
				case ']':
					$this->testPop($state);
					$sindex--;
					if($end == qp_base) {
						$base = $str;
					}
					$str = "";
					break;

				case '\'':
					if($end == qp_strn) {
						$this->testPop($state);
						$sindex--;
						$end = $state[$sindex];

						if($end == qp_iden)
							$iden[] = $str;

						$str = "";
					}
					else {
						$this->testPush($state, qp_strn);
						$sindex++;
					}
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
						$sindex--;
						$cpart->setEdge($edge);
					}
					else
					if($cpart->parent == null && $cpart->child == null)
						// this is what happens when it's a single query
						$cpart->setParent($base, $type, $iden, $xtra);

					$this->testPop($state);
					$sindex--;
					$str = "";
				break;

				default:
					if($end == qp_block || $end == qp_rela) {
						$this->testPush($state, qp_stat);
						$sindex++;
					}

					$str .= $ch;
				break;
				}
			}

			return $cpart;
		}

		private function testPush(&$a, $s)
		{
			global $stuff;
			array_push($a, $s);
			foreach($a as $c) {
				echo $stuff[$c] . " ";
			}
			echo "<br />";
		}

		private function testPop(&$a)
		{
			global $stuff;
			array_pop($a);
			foreach($a as $c) {
				echo $stuff[$c] . " ";
			}
			echo "<br />";
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
