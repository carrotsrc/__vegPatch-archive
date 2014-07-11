<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	class nodeviewPanel extends Panel
	{
		private $resourceManager;
		private $nvref;
		private $panelUrlParams;

		public function __construct()
		{
			parent::__construct();
			$this->setModuleName('nodeview');
			$this->panelUrlParams = array();
		}

		public function loadTemplate()
		{
			$this->fallbackNormal();
			$this->includeTemplate("templates/basic.php");
		}

		public function initialize($params = null)
		{
			$vars = $this->argVar(array('sinvn' => 'sinvn',  // node
						    'sinvt' => 'sinvt',  // type
						    'sinva' => 'sinva',  // anchor
						    'sinvo' => 'sinvo', // operation
						    'sinvi' => 'sinvi', // operations on id 
						    'nvc' => 'sinrf'),  // instance
						$params);
			/*
			*  this init is unusual because
			*  it is a floating panel and
			*  anchored component
			*  TODO:
			*  think of a better way
			*  + expensive operations
			*/
			if($vars->sinrf == null) {
				/* NVSet plugin wasn't run
				*  so we have to generate the nvc
				*  reference number here
				*/
				$width = floor((PHP_INT_SIZE<<3)/3); // expensive operation
				// fairly cheap operations 
				$vars->sinrf = $this->instanceId<<($width<<1);
				$vars->sinrf ^= ($params['layout']->getId()<<$width);
				$vars->sinrf ^= ($params['area']->getId());
			}

			// request types or resources
			$this->addComponentRequest(1, $vars->__get(null));


			if($vars->sinvo == PNL_CM_READ) // read a resource node
				$this->addComponentRequest(2, $vars->__get(null));
			else
			if($vars->sinvo == PNL_CM_CREATE) // add a resource to a node
				$this->addComponentRequest(3, $vars->__get(null));
			else
			if($vars->sinvo == PNL_CM_DELETE)
				$this->addComponentRequest(4, $vars->__get(null));
			else
			if($vars->sinvo == 8)
				$this->addComponentRequest(8, 101);

			$this->addComponentRequest(9, 101);


			parent::initialize($params);
		}

		public function applyRequest($result)
		{
			foreach($result as $rs){
				switch($rs['jack']) {
				case 1:
					$res = $rs['result'];
					if(!is_array($res))
						break;
					$sz = $res[0];
					$this->addTParam('trail', array_slice($res, 1, $sz));
					$t = array_slice($rs['result'], 1+$sz);
					$this->addTParam('resources', $t);
					if(isset($_GET['sinvo']) && $_GET['sinvo']  == 5) {
						$this->addTParam("snode", $_GET['sinvi']);
					}
				break;
				case 2:
				case 3:
				case 4:
					$res = $rs['result'];

					$res[1]->setCommonGroup($this->getCommonGroup());
					$this->addTParam('panel',array($res[0], $res[1]->getTemplate()));
					$this->addOnloadScript($res[1]->jsOnLoad());
				break;

				case 9:
					$res = $rs['result'];
					if(is_array($res))
						foreach($res as $param)
							$this->panelUrlParams[$param] = null;
				break;

				case 8:
					if(isset($_GET['sinvi'])) {
						$this->addTParam('types', $rs['result']);
						$this->addTParam('snode', $_GET['sinvi']);
					}
				break;

				}
			}
		}

		public function setAssets()
		{
			$this->addAsset('css', "templates/nvstyle.css");
		}

		private function fallbackNormal()
		{
			$qstr = QStringModifier::modifyParams(array('sinvn' => null, 'sinvi' => null,
									'sinvo' => null, 'sinvt' => null));
			$this->addFallbackLink('open', $qstr);

			$qstr = QStringModifier::modifyParams(array('sinvi' => null,
									'sinvo' => null));
			$this->addFallbackLink('typesel', $qstr);

			$qstr = QStringModifier::modifyParams(array('sinvi' => null,
									'sinvo' => null));
			$this->addFallbackLink('oot', $qstr);

			$qstr = QStringModifier::modifyParams(array_merge($this->panelUrlParams,
									array('sinvi' => null, 'sinvo' => PNL_CM_CREATE)));
			$this->addFallbackLink('add', $qstr);

			$qstr = QStringModifier::modifyParams(array_merge($this->panelUrlParams,
									array('sinvi' => null, 'sinvo' => PNL_CM_READ)));
			$this->addFallbackLink('view', $qstr);

			$qstr = QStringModifier::modifyParams(array_merge($this->panelUrlParams,
									array('sinvi' => null, 'sinvo' => 5)));
			$this->addFallbackLink('node', $qstr);

			$qstr = QStringModifier::modifyParams(array_merge($this->panelUrlParams,
									array('sinvi' => null, 'sinvo' => PNL_CM_DELETE)));
			$this->addFallbackLink('remove', $qstr);
		}
	}
?>
