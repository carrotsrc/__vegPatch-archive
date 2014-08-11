<?php

/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	require_once($_SERVER['DOCUMENT_ROOT'] . "/ksysconfig.php");
	/*
	* INITIALIZATION TREE
        * Check to see if we display PHP error
	* Get the flag to minimise the amount of
	* static calls
	* TODO
	* CLEAN THIS UP
	*/
	$flag = SystemConfig::$KS_FLAG;
	if($flag&KS_PHP_ERROR)
	{
		error_reporting(E_ALL);
		ini_set('display_errors', '1');
	}


	// We need these regardless
	$appRootPath = SystemConfig::appRootPath();
	define('KLOG_PATH', $appRootPath.".vlog/vplog");

	require($appRootPath."system/db/db.php");
	require($appRootPath."system/file/filemanager.php");
	require($appRootPath."system/dbacc.php");
	require($appRootPath."system/log.php");
	require($appRootPath."system/managers.php");
	require($appRootPath."system/appconfig.php");
	require($appRootPath."system/plugin/chanman.php");
	require($appRootPath."system/resource/resman.php");

	// global helpers
	include($appRootPath."system/helpers/session.php");
	include($appRootPath."system/helpers/httpheader.php");
	include($appRootPath."system/helpers/klog.php");


	Session::start();

	/* Debug for nuking the session variables */
	if(isset($_GET['cache'])) {
		if($_GET['cache'] == "nuke") {
			Session::wipe();
		}

		unset($_GET['cache']);
	}


	if(isset($_GET['nodym'])) {
		$nodym = $_GET['nodym'];

		if($nodym == 0)
			Session::uset('nodym');
		else
			Session::set('nodym', $nodym);

		// Switch has been made so can remove it from the
		// params
		unset($_GET['nodym']);
	}

	//	If this is set then the current location is recorded in the session
	if($flag&KS_TRACK) {
		$script = $_SERVER['SCRIPT_NAME'];
		Session::set('track', array($script, $_GET));
	}

	//	If we are needing to access modules
	if($flag&KS_MOD)
	{
		include($appRootPath."system/libload.php");
		include($appRootPath."system/cblank.php");
		include($appRootPath."system/structure/blocks/schemablock.php");
		include($appRootPath."system/structure/areas/areaman.php");
		include($appRootPath."system/helpers/qstringmodifier.php");
		include($appRootPath."system/helpers/strsan.php");
		StrSan::init();
	}
	include($appRootPath."system/module/modman.php");

	//	If we are needing to access surrounds
	if($flag&KS_SURROUND)
	{
		//	If we don't have modules
		if(!($flag&KS_MOD) && !($flag&KS_SURROUND_ONLY))
		{
			include($appRootPath."system/structure/blocks/templateholder.php");
			include($appRootPath."system/structure/blocks/assetholder.php");
		}
	}
	include($appRootPath."system/structure/areas/surroundman.php");

	if($flag&KS_ASSETS && !($flag&KS_MOD))
		include($appRootPath."system/structure/blocks/assetholder.php");


	//	Only required if app is loading a page
	if($flag&KS_IS_PAGE)
	{
		include($appRootPath."system/helpers/vpxml.php");
		include($appRootPath."system/layout/layman.php");
		include($appRootPath."system/structure/page.php");
		include($appRootPath."system/helpers/alinkgen.php");
	}

	if($flag&KS_DEBUG_MICRO)
		include($appRootPath."system/debugmicro.php");

	/*
	* END INITIALIZATION TREE
	*/
	
	
	
	/*
	* The root application that handles all the different parts
	* it is a little better now
	*/
	class App
	{
		private $appDB;

		private $page;

		private $resourceManager;
		private $channelManager;
		private $pluginManager;
		private $layoutManager;
		private $areaManager;
		private $appConfig;

		private $params;
		
		public function __construct()
		{
			$this->appDB = null;
			$this->page = null;
			$this->resoureManager = null;
			$this->channelManager = null;
			$this->pluginManager = null;
			$this->layoutManager = null;
			$this->areaManager = null;
			$this->appConfig = null;
			$this->params = array();
		}
		
		private function initDatabase()
		{
			$this->appDB = core_create_db('mysql');
			if(!$this->appDB->connect(SystemConfig::$dbcUsername, SystemConfig::$dbcPassword))
				return false;

			if(!$this->appDB->selectDatabase(SystemConfig::$dbcDatabase))
				return false;

			return true;
		}
		
		private function getConfiguration()
		{
			$config = array();
			$query = "SELECT * FROM rootconfig;";
			$r = $this->appDB->sendQuery($query);
			
			if(!$r)
				die("Major malfunction: root configuration error");

			foreach($r[0] as $key => $value)
				$config[$key] = $value;


			$config['approot'] = SystemConfig::appRootPath();
			$this->appConfig = new AppConfig($config);
		}

		public function getAppDB()
		{
			return $this->appDB;
		}

		public function init()
		{
			$flag = SystemConfig::$KS_FLAG;


			if(!$this->initDatabase()) {
				echo "Database error<br />";
				return false;
			}

			//  Load configuration
			$this->getConfiguration();
			Managers::setAppConfig($this->appConfig);
			//  Set the relative path for all asset holders (bit hacky)
			AssetHolder::setRelativePath($this->appConfig->setting('libshare'));
			//  Load root assets.
			$this->initManagers($flag);

			//  Run the request channel
			if(!$this->runChannel('_on_request')) {
				echo "_on_request event failure<br />";
				return false;
			}

			//  Run the area access channel
			if($flag&KS_MOD && !($flag&KS_ASSETS))
				if(!$this->runChannel('_on_area_access')) {
					KLog::error("_on_area_access channel failure");
					echo "<br />_on_area_access event failure</br />";
					return false;
				}

			if(!($flag&KS_IS_PAGE))
				return true;

			return true;
		}

		public function getPage()
		{
			// Add the root parameters for the template 
			$this->params['root']['title'] = $this->appConfig->setting('title');
			$this->params['root']['media'] = "http://".SystemConfig::appServerRoot("library/media/");
			$arc = Session::get('arc');
			if($arc == null) {
				$result = $this->appDB->sendQuery("SELECT type, value FROM rootasset;", false, false);
				$arc = array('js' => array(), 'css' => array());
				foreach($result as $r)
					$arc[$r['type']][] = $r['value'];
			}

			$this->params['arc'] = $arc;

			if(!$this->runChannel('_on_page_request')) {
				KLog::error("_on_page_request channel failure");
				return false;
			}
			if(!isset($this->params['page']))
				return null;

			$this->page = $this->params['page'];

			// Add the app config
			return $this->page;
		}

		public function getAsset()
		{
			$this->runChannel('_on_asset_request');
			if(isset($this->params['response']))
				return $this->formatAsset($this->params['response']);
			else
				return null;
		}

		public function requestInterface()
		{
			if(!$this->runChannel('_on_interface_request'))
				return false;

			return $this->params['response'];
		}
		
		private function loadPanelAsset($mId, $aType, $aName)
		{
		}

		private function formatAsset($asset)
		{
			if($asset == null)
				return null;

			$asset = str_replace("__URL_AJAX_REQ__", "http://".SystemConfig::appServerRoot($this->appConfig->setting('ajaxrequest')), $asset);
			$asset = str_replace("APP-MEDIA",  "http://".SystemConfig::appServerRoot("library/media/"), $asset);
			$rid = Session::get('aid');
			if($rid != null) {
				$aid = $this->resourceManager->getHandlerRef($rid);
				$asset = str_replace("__KAID__", $aid, $asset);
			}
			return $asset;
		}

		private function initManagers($flag)
		{
			$this->resourceManager = new ResMan($this->appDB);
			Managers::setResourceManager($this->resourceManager);
		}

		private function runChannel($hook)
		{
			$chRid = $this->resourceManager->queryAssoc("Channel('$hook'){r};");
			if(!$chRid)
				return false;

			$id = $chRid[0]['ref'];
			$channel = core_get_channel($id, $this->appDB);
			if($channel == null) {
				echo "Failed to run channel";
				return false;
			}

			$result = $channel->runSignal($this->params);

			if(!$result)
				return false;

			$this->params = $result;
			return true;
		}

	}
?>
