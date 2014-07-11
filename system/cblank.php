<?php

/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	class CBlank
	{
		private $objData = array();
		
		public function __set($property, $value)
		{
			$this->objData[$property] = $value;
		}
		
		public function __get($property)
		{
			if($property == null)
				return $this->objData;

			if(array_key_exists($property, $this->objData))
			{
				return $this->objData[$property];
			}
			
			return null;
		}
		
		public function __isset($property)
		{
			return isset($this->objData[$property]);
		}
		
		public function __unset($property)
		{
			unset($this->objData[$property]);
		}
	}
?>
