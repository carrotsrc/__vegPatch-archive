<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	require("assetholder.php");
	require("templateholder.php");
	
	abstract class SchemaBlock implements IAsset, ITemplate
	{
		protected $aHolder;
		protected $tHolder;
		
		protected $lParams;
		protected $tParams;
		
		protected $id;
		protected $absolutePath;
		protected $localRelativePath;
		
		protected $fValues;
		
		public function __construct()
		{
			$this->aHolder = new AssetHolder();
			$this->tHolder = new TemplateHolder();
			$this->lParams = array();
			$this->tParams = new CBlank();
			$this->id = null;
			$this->absolutePath = null;
			$this->fValues = array();
		}

		public function setId($id)
		{
			$this->id = $id;
			$this->addTParam('_pnid', $id);
		}		

		public final function setAbsolutePath($path)
		{
			$this->absolutePath = $path;
			$this->aHolder->setAbsolutePath($path);
		}

		public final function setLocalRelative($path)
		{
			$this->localRelativePath = $path;
			$this->aHolder->setLocalRelativePath($path);
		}

		public final function getAbsolutePath()
		{
			return $this->absolutePath;
		}		

		public abstract function setAssets();

		//	Asset Interface inplementations
		public final function getAssets($type = null)
		{
			return $this->aHolder->getAssets($type);
		}

		public final function getAssetPath($type, $name)
		{
			return $this->aHolder->getAssetPath($type, $name);
		}

		// Template Inteface
		public abstract function loadTemplate();

		public final function getTemplate()
		{
			return $this->tHolder->getTemplate();
		}

		protected final function readTemplate($filename)
		{
			$path = SystemConfig::libraryPath().$this->absolutePath.$filename;
			return $this->tHolder->readTemplate($path);
		}

		protected final function includeTemplate($filename)
		{
			$this->generateFallbackLink();
			$path = SystemConfig::libraryPath().$this->absolutePath.$filename;
			return $this->tHolder->includeTemplate($path, $this->tParams);
		}

		public final function addTParam($name, $value)
		{
			$this->tParams->__set($name, $value);
		}

		public function initialize($params = null)
		{
			$this->setAssets();
		}

		protected abstract function generateFallbackLink();
	}

?>
