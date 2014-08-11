<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	define('PNL_CM_NONE', 0);
	define('PNL_CM_CREATE', 1);
	define('PNL_CM_READ', 2);
	define('PNL_CM_UPDATE', 3);
	define('PNL_CM_DELETE', 4);
	define('PNL_CM_TERM', 5);

	abstract class Panel extends SchemaBlock implements IModArg
	{
		/* TODO
		*  These property names
		*  are screwed. Confusing to
		*  work with. What do they mean?
		*  Too much thought and memory
		*  required
		*/
		protected $moduleName;
		protected $moduleId;
		protected $componentId;
		protected $instanceId;
		protected $moduleSpace;

		protected $globalParams;

		protected $jsCommon;
		protected $commonGroup;

		private $requestList;

		protected $crud;

		protected $onloadScripts;

		protected $lockedContents;
		protected $ploadVars;

		public function __construct()
		{
			parent::__construct();
			$this->moduleId = -1;
			$this->jsCommon = null;
			$this->jsObject = null;
			$this->requestList = array();
			$this->crud = 0;
			$this->onloadScripts = array();
		}
		
		public function setId($id)
		{
			parent::setId($id);
			$this->addTParam('_pmod', $this->moduleName.$id);
		}

		public final function setComponentId($id)
		{
			$this->componentId = $id;
			$this->addTParam('_cmpt', $id);
		}

		public final function getComponentId()
		{
			return $this->componentId;
		}

		public final function setModuleId($id)
		{
			if($this->moduleId == -1)
				$this->moduleId = intval($id);
		}

		public final function getModuleId()
		{
			return $this->moduleId;
		}

		public final function setInstanceId($id)
		{
			$this->instanceId = $id;
			$this->addTParam('_inst', $id);
		}

		public final function getInstanceId()
		{
			return $this->instanceId;
		}

		public final function setModuleName($name)
		{
			$this->moduleName = $name;
			$this->addTParam('_name', $name);
		}

		public final function setModuleSpace($space)
		{
			$this->moduleSpace = $space;
			$this->addTParam('_space', $space);
		}

		public final function setCommonGroup($group)
		{
			$this->commonGroup = $group;
			$this->addTParam('_gnid', $group);
		}

		public final function getCommonGroup()
		{
			return $this->commonGroup;
		}

		protected final function addAsset($type, $value, $name = null)
		{
			if($name == null) {
				$atoms = explode("/", $value);
				$name = $atoms[sizeof($atoms)-1];
			}

			if($value[0] == '/') {
				if($value[1] == 'G' && $value[2] == '/')
					$name= "glob/".$name;
				else
					$name = $this->moduleSpace."/".$name;
			}
			$this->aHolder->addAsset($type, $value, $name, 1, $this->moduleId);
		}

		public function jsOnLoad()
		{
			if($this->jsCommon == null && sizeof($this->onloadScripts) == 0)
				return null;

			$onload = "";
			if($this->jsCommon != null) {
				$cint = $this->jsCommon;
				$id = $this->id;
				$cmpt = $this->componentId;
				$inst = $this->instanceId;
				$onload .= "VPLib.CommonInterface.register({$this->id}, {$this->componentId}, {$this->instanceId}, {$this->commonGroup}, '{$this->jsCommon}');\n";
			}
			
			if(sizeof($this->onloadScripts) > 0)
				foreach($this->onloadScripts as $line)
					$onload .= "$line\n";

			return $onload;
		}

		protected function addOnloadScript($script)
		{
			$this->onloadScripts[] = $script;
		}

		protected function generateFallbackLink()
		{
			return;
		}

		protected final function addFallbackLink($name, $str)
		{
			if(!isset($this->tParams->_fallback))
				$this->tParams->_fallback = new CBlank();

			$this->tParams->_fallback->__set($name, $this->appendGlobalParams($str));
		}

		public final function addGlobalParam($param, $value)
		{
			if($this->globalParams == null)
				$this->globalParams = array();

			if($value == null) {
				unset($this->globalParams[$param]);
				return;
			}
			$this->globalParams[$param] = $value;

		}

		private final function appendGlobalParams($str)
		{
			if($this->globalParams == null)
				return $str;

			foreach($this->globalParams as $k => $param)
				$str .= "&$k=$param";

			return $str;
		}

		protected final function isJSEnabled()
		{
			$sw = Session::get('nodym');
			if($sw == null || $sw == 1)
				return true;

			return false;
		}

		protected final function addComponentRequest($jack, $params)
		{
			/*
			*  TODO:
			*  this should be a class instead of
			*  using an array for cmpt requests
			*
			*  or maybe:
			*  make it so single request is sent to
			*  link between the panel and the
			*  component directly
			*/
			$this->requestList[] = array(	'cmpt'	=> $this->componentId,
							'inst'	=> $this->instanceId,
							'jack'	=> $jack,
							'params'=> $params); 
		}
		public final function componentRequests()
		{
			if(sizeof($this->requestList) == 0)
				return null;

			return $this->requestList;
		}

		final public function argVar($vars, $args = null)
		{
			if(!is_array($vars))
				return null;

			$r = new CBlank();
			foreach($vars as $key => $value) {
				$v = null;
				if(isset($_GET[$key]))
					$v = $_GET[$key];
				else
				if(isset($args[$key]))
					$v = $args[$key];

				if(isset($this->ploadVars))
					$v = $this->ploadVars[$key];

				if($v == null)
					continue;

				$r->__set($value, $v);
			}

			return $r;
		}

		abstract public function applyRequest($result);
		final public function getAssetPaths($type = null)
		{
			$assets = $this->aHolder->getAssets();
			$gpath = $this->aHolder->getGlobalRelativePath();
			$paths = array();
			foreach($assets as $asset) {
				$path = "";
				if($type != null && $asset['type'] != $type)
					continue;

				$val = $asset['value'];
				if($val[0] == '/') {
					if(strlen($val) >= 3) {
						if($val[1] == 'G' && $val[2] == '/')
							$path = $gpath . "/".substr($val, 3, strlen($val)-1);
						else
							$path = $this->localRelativePath . substr($val, 1, strlen($val));
					}
				}
				else
					$path = $this->absolutePath . $val;

				$paths[] = array('name' => $asset['name'], 'value' => $path);

			}

			return $paths;
		}

		public function setCrudMode($mode)
		{
			// externally set the panel's mode
			// through standardised CRUD
			$this->crud = $mode;

			// override this method and return
			// false if the panel has no idea
			// of CRUD
			return true;
		}

		public function getCrudMode()
		{
			return $this->crud;
		}

		public function getUrlParams()
		{
			$params = array();
			foreach($this->globalParams as $param)
				$params[] = $param[0];

			return $params;
		}

		public function globalParamStr($combined = true)
		{
			$sz = sizeof($this->globalParams)-1;
			if($sz < 0)
				return;

			$str = "";

			if($combined)
				$str = "&";


			foreach($this->globalParams as $k => $v) {
				$str .= "$k=$v";

				if($sz-- > 0)
					$str.= "&";
			}

			return $str;
		}

		public function setLoadVars($vars)
		{
			$split = explode(";", $vars);
			foreach($split as &$p) {
				if($p == "")
					continue;
				$pair = explode(":", $p);
				$this->ploadVars[$pair[0]] = trim($pair[1]);
			}
		}
	}
?>
