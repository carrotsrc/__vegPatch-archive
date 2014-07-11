<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	abstract class Container
	{
		public $type;
		public $content;
		public $attr;
		
		public function __construct()
		{
			$this->type = 1;
			$this->props = null;
		}

		public function isLeaf()
		{
			if(is_array($this->content))
				return false;

			return true;
		}

		public function setType($nType)
		{
			$this->type = $nType;
		}

		public function addAttribute($attr, $value)
		{
			if($this->attr == null)
				$this->attr = array();

			$this->attr[$attr] = $value;
		}

		public function setContent($nContent)
		{
			$this->content = $nContent;
		}


		abstract public function generateHTML($index, $path);
	}
?>
