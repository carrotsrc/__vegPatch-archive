<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	define('VPX_ROOT', 0);
	define('VPX_TAG', 1);
	define('VPX_ATTR', 2);
	define('VPX_ATTR_VALUE', 3);
	define('VPX_STR', 4);
	define('VPX_COMMENT', 5);

	class VPTag
	{
		public $element = null;
		public $attributes = null;
	}

	class VPXML
	{
		private $xml;
		private $len;
		private $offset;
		
		private $state;
		private $cState;

		public function init($xml)
		{
			$this->xml = $xml;
			$this->len = strlen($xml);
			$this->offset = 0;
			$this->state = array();
			$this->cState = VPX_ROOT;
		}

		public function getNextTag()
		{
			if($this->offset >= $this->len)
				return null;

			$cStr = "";
			$attr = 0;
			$attributePool = array();
			$cAttribute = "";
			$pLoc = -1;
			$cCom = "";

			$cTag = null;

			for($i = $this->offset; $i < $this->len; $i++)
			{
				$ch = $this->xml[$i];
				if($this->cState == VPX_COMMENT) {
					if($ch == '-' && $this->xml[$i+1] == '-' && $this->xml[$i+2] == '>') {
						$this->popState();
						$this->popState();
						$cTag = new VPTag();
						$cTag->element = "_comment_";
						$cTag->attributes['content'] = $cCom;
						$this->offset += (($i+3)-$this->offset);
						$cCom = "";
						return $cTag;
					}
					else {
						$cCom .= $ch;
						continue;
					}
				}

				switch($ch)
				{
				case ' ':
					if($this->cState == VPX_STR || $this->cState == VPX_ROOT)
						$cStr .= $ch;
					else
					if($this->cState == VPX_TAG)
					{
						// We are in tag state
						if(strlen($cStr) == 0)
							continue;

						$cTag = new VPTag();
						$cTag->element = $cStr;
						$this->pushState(VPX_ATTR);
						$cStr = "";
					}
					else
					if($this->cState == VPX_ATTR)
					{
						// We are in attribute state
						if(strlen($cStr) == 0)
							continue;

						$cAttribute = $cStr;
						$cStr = "";
					}
					else
					if($this->cState == VPX_ATTR_VALUE)
					{
						if(strlen($cStr) == 0)
							continue;
						$attributePool[$cAttribute] = $cStr;
						$cAttribute = "";
						$cStr = "";
						$this->popState();
					}
					else
						continue;
				break;

				case '=':
					if($this->cState == VPX_STR)
					{
						$cStr .= $ch;
						continue;
					}

					if($this->cState == VPX_ATTR)
					{
						if(strlen($cStr) > 0)
						{
							$cAttribute = $cStr;
							$cStr = "";
						}
						$this->pushState(VPX_ATTR_VALUE);
					}
					else
						continue;
				break;

				case '<':
					if(strlen($cStr) > 0) {
						$cTag = new VPTag();
						$cTag->element = "_text_";

						$cTag->attributes= array('content' => $cStr);
						$this->offset += ($i-$this->offset);
						return $cTag;
					}

					if($this->cState == VPX_STR) {
						$cStr .= $ch;
						continue;
					}

					$this->pushState(VPX_TAG);
				break;

				case '>':
					if($this->cState == VPX_STR)
					{
						$cStr .= $ch;
						continue;
					}

					if($this->cState == VPX_ATTR_VALUE)
					{
						$attributePool[$cAttribute] = $cStr;
						$cAttribute = "";
						$this->popState();
						$this->popState();
					}
					else
					if($this->cState == VPX_ATTR)
					{
						$this->popState();
					}
					else
					if($this->cState == VPX_TAG)
					{
						$cTag = new VPTag();
						$cTag->element = $cStr;
					}

					$this->popState();
					if($this->cState == VPX_ROOT)
					{
						$this->offset += (($i-$this->offset)+1);
						$cTag->attributes = $attributePool;
						return $cTag;
					}
				break;

				case '\'':
				case '"':
					if($this->cState == VPX_STR)
					{
						if(($len = strlen($cStr)) > 0 && $cStr[$len-1] == '\\')
							$cStr .= $ch;
						else
							$this->popState();
					}
					else
					if($this->cState == VPX_ROOT)
						$cStr .= $ch;
					else
						$this->pushState(VPX_STR);
				break;

				case '\n':
				case '\t':
				break;

				case '/': // end of empty tag
					if($this->cState == VPX_ATTR_VALUE)
					{
						$attributePool[$cAttribute] = $cStr;
						$cAttribute = "";
						$this->popState();
					}
					else
					if($this->cState == VPX_ATTR)
						continue;
					else
					if($this->cState == VPX_TAG || $this->cState == VPX_STR)
						$cStr .= $ch;
				break;

				case '\\':
					if($this->cState == VPX_STR)
					{
						if(++$i < $this->len)
							$cStr .= $this->xml[$i];
					}
				break;

				default:
					$cStr .= $ch;
					if($this->cState == VPX_TAG && $cStr == "!--") {
						$cStr = "";
						$this->pushState(VPX_COMMENT);
					}
				break;
				}
			}

			$this->offset = $this->len;

			if($cStr != "")
			{
				$cTag = new VPTag();

				$cTag->element = "_text_";
				$cTag->attributes = array('content' => $cStr);
				return $cTag;
			}

			return null;
		}

		private function pushState($s)
		{
			$this->state[] = $s;
			$this->cState = $s;
		}

		private function popState()
		{
			array_pop($this->state);
			if(sizeof($this->state) > 0)
				$this->cState = end($this->state);
			else
				$this->cState = 0;
		}

		public function rewind()
		{
			$this->offset = 0;
			$this->cState = VPX_ROOT;
			$this->state = array();
		}

		public function desanitizeString($string)
		{
			$dirtyString = "";
			$sLen = strlen($string);

			for($i = 0; $i < $sLen; $i++)
			{
				if($string[$i] == '\\')
				{
					if(++$i < $sLen)
						$dirtyString .= $string[$i];
				}
				else
					$dirtyString = $string[$i];
			}

			$dirtyString = str_replace('\\n', '\n', $dirtyString);
			$dirtyString = str_replace('\\t', '\t', $dirtyString);

			return $dirtyString;
		}
	}
?>
