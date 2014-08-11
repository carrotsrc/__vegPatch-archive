<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	require_once("iasset.php");
	
	class AssetHolder implements IAsset
	{
		protected $assets;
		protected $absolutePath;
		protected static $globalRelativePath = null;
		protected $localRelativePath;
		protected $formatScheme;

		public function __construct($owner = 0, $caller = 0)
		{
			$this->assets = array();
			$this->absolutePath = null;
			$this->localRelativePath = null;
			$this->formatScheme = array();
			$this->owner = $owner;
			$this->caller = $caller;
		}
		
		public function setAbsolutePath($path)
		{
			$this->absolutePath = $path;
		}

		static function setRelativePath($path)
		{
			self::$globalRelativePath = $path;
		}

		public function getGlobalRelativePath()
		{
			return self::$globalRelativePath;
		}

		public final function setLocalRelativePath($path)
		{
			$this->localRelativePath = $path;
		}

		public final function addAsset($type, $value, $name = null)
		{
			if($type != 'mod')
			{
				if($name == null)
				{
					$atoms = explode('/', $value);
					$name = end($atoms);
				}
			}

			$this->assets[] = array('type'  => $type,
						'name'  => $name,
						'value' => $value);
		}

		public final function getAssets($type = null)
		{
			if($type == null)
				return $this->assets;

			$aAssets = array();

			foreach($this->assets as $asset)
				if($asset['type'] == $type)
					$aAssets[] = $asset;
			
			if(sizeof($aAssets) == 0)
				return null;
			
			return $aAssets;
		}
		
		public final function getAssetPath($type, $name)
		{
			$assets = $this->getAssets($type);
			foreach($assets as $asset)
				if($asset['name'] == $name)
					return $asset['value'];
						
			return null;
		}
		

		public function getNumAssets()
		{
			return sizeof($this->assets);
		}

		private function nameExists($name, $owner = null)
		{
			if($owner == null)
				$owner = 1;

			foreach($this->assets as $asset)
				if($asset['name'] == $name && $asset['owner'] == $owner)
					return true;

			return false;
		}

	}
?>
