<?php
/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	class KTag
	{
		public $tag;
		public $properties;
		public function addProperty($property, $value)
		{
			$this->properties[] = array($property, $value);
		}
		public function printMe()
		{
			$rtn = "&lt;".$this->tag;
			if(is_array($this->properties) && sizeof($this->properties) > 0)
				foreach($this->properties as $p)
					$rtn .= " ".$p[0]."=\"".$p[1]."\"";

			$rtn .= "&gt";

			return $rtn;
		}
	}

	class KXML
	{
		private $xml;
		private $len;
		private $offset;
		
		private $state;
		private $cState;

		private $tags;
		private $properties;

		private $mode;
		public function init($xml)
		{
			$this->xml = $xml;
			$this->len = strlen($xml);
			$this->offset = 0;
			$this->state = array();
			$this->cState = 0;
			$this->mode = 1;
		}

		public function switchMode($mode)
		{
			$this->mode = $mode;
		}

		public function setTags($tags)
		{
			$this->tags = $tags;
		}

		public function addTag($tag)
		{
			foreach($this->tag as $t)
				if($t == $tag)
					return;

			$this->tags[] = $tag;
		}

		public function addTags($tags)
		{
			foreach($tags as $t) {
				$dup = false;
				foreach($this->tags as $c)
					if($c == $t) {
						$dup = true;
						break;
					}

				if($dup)
					continue;
				$this->tags[] = $t;

			}
		}

		public function setProperties($properties)
		{
			$this->properties = $properties;
		}

		public function addProperties($properties)
		{
			foreach($properties as $p) {
				$dup = false;
				foreach($this->properties as $c)
					if($c == $p) {
						$dup = true;
						break;
					}

				if($dup)
					continue;
				$this->properties[] = $p;

			}
		}

		public function addProperty($property)
		{
			foreach($this->properties as $p)
				if($p == $property)
					return;

			$this->properties[] = $property;
		}

		private function isTag($tag)
		{
			foreach($this->tags as $i => $t)
				if($t == $tag)
				{
					if($this->mode == 0)
						return $i;
					else
						return $tag;
				}

			if($this->mode == 0)
				return -1;
			else
				return "?".$tag."?";
		}

		private function isProperty($prop)
		{
			foreach($this->properties as $i => $p)
				if($p == $prop)
				{
					if($this->mode == 0)
						return $i;
					else 
						return $prop;
				}
			if($this->mode == 0)
				return -1;
			else
				return "?".$prop."?";
		}

		public function getNextTag()
		{
			if($this->offset >= $this->len)
				return null;

			$cStr = "";
			$attr = 0;
			$propertyPool = array();
			$cProperty = array();
			$pLoc = -1;

			$cTag = null;

			for($i = $this->offset; $i < $this->len; $i++)
			{
				$ch = $this->xml[$i];
				switch($ch)
				{
				case ' ':
					if($this->cState == 4 || $this->cState == 0)
						$cStr .= $ch;
					else
					if($this->cState == 1)
					{
						// We are in tag state
						if(strlen($cStr) == 0)
							continue;

						$cTag = new KTag();
						$cTag->tag = $this->isTag($cStr);
						$this->pushState(2);
						$cStr = "";
					}
					else
					if($this->cState == 2)
					{
						// We are in property state
						if(strlen($cStr) == 0)
							continue;

						$cProperty[] = $this->isProperty($cStr);
						$cStr = "";
					}
					else
					if($this->cState == 3)
					{
						if(strlen($cStr) == 0)
							continue;

						$cProperty[] = $cStr;
						array_push($propertyPool, array($cProperty[0], $cProperty[1]));
						$cProperty = array();
						$cStr = "";
						$this->popState();
					}
					else
						continue;
				break;

				case '=':
					if($this->cState == 4)
					{
						$cStr .= $ch;
						continue;
					}

					if($this->cState == 2)
					{
						if(strlen($cStr) > 0)
						{
							$cProperty[] = $this->isProperty($cStr);
							$cStr = "";
						}
						$this->pushState(3);
					}
					else
						continue;
				break;

				case '<':
					if(strlen($cStr) > 0)
					{
						$cTag = new KTag();
						if($this->mode == 0)
							$cTag->tag = -2;
						else
							$cTag->tag = "_text_";

						$cTag->property = $cStr;
						$this->offset = $this->offset+($i-$this->offset);
						return $cTag;
					}
					if($this->cState == 4)
					{
						$cStr .= $ch;
						continue;
					}

					$this->pushState(1);
				break;

				case '>':
					if($this->cState == 4)
					{
						$cStr .= $ch;
						continue;
					}

					if($this->cState == 3)
					{
						$cProperty[] = $cStr;
						array_push($propertyPool, array($cProperty[0], $cProperty[1]));
						$cProperty = array();
						$this->popState();
						$this->popState();
					}
					else
					if($this->cState == 2)
					{
						$this->popState();
					}
					else
					if($this->cState == 1)
					{
						$cTag = new KTag();
						$cTag->tag = $this->isTag($cStr);
					}

					$this->popState();
					if($this->cState == 0)
					{
						$this->offset = $this->offset + (($i-$this->offset)+1);
						foreach($propertyPool as $p)
							$cTag->addProperty($p[0], $p[1]);
						return $cTag;
					}
				break;

				case '\'':
				case '"':
					if($this->cState == 4)
					{
						if($cStr[strlen($cStr)-1] == '\\')
							$cStr .= $ch;
						else
							$this->popState();
					}
					else
					if($this->cState == 0)
						$cStr .= $ch;
					else
						$this->pushState(4);
				break;

				case '\n':
				case '\t':
				break;

				case '/':
					if($this->cState == 3)
					{
						$cProperty[] = $cStr;
						$propertyPool[] = $cProperty;
						$cProperty = array();
						$this->popState();
					}
					else
					if($this->cState == 2)
						continue;
					else
					if($this->cState == 1 || $this->cState == 4)
						$cStr .= $ch;
				break;

				case '\\':
					if($this->cState == 4)
					{
						if(++$i < $this->len)
							$cStr .= $this->xml[$i];
					}
				break;

				default:
					$cStr .= $ch;
				break;
				}
			}

			$this->offset = $this->len;

			if($cStr != "")
			{
				$cTag = new KTag();

				if($this->mode == 0)
					$cTag->tag = -2;
				else
					$cTag->tag = "_text_";

				$cTag->property = $cStr;
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
				$this->cState = $this->state[sizeof($this->state)-1];
			else
				$this->cState = 0;
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
