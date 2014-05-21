<?php

/* (C)opyright 2014, Carrotsrc.org
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

	require_once($appRootPath."system/koda/koda.php");
	require_once($appRootPath."system/dbacc.php");
	require_once($appRootPath."system/log.php");
	require_once($appRootPath."system/managers.php");
	require_once($appRootPath."system/appconfig.php");
	require_once($appRootPath."system/plugin/chanman.php");
	require_once($appRootPath."system/resource/resman.php");

	// global helpers
	include_once($appRootPath."system/helpers/session.php");
	include_once($appRootPath."system/helpers/httpheader.php");
	include_once($appRootPath."system/helpers/klog.php");


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

	//	We are using users (requires sessions)
	if($flag&KS_USER)
	{
		include_once($appRootPath."system/user/userman.php");
		include_once($appRootPath."system/user/roleman.php");
	}

	//	If we are needing to access modules
	if($flag&KS_MOD)
	{
		include_once($appRootPath."system/libload.php");
		include_once($appRootPath."system/cblank.php");
		include_once($appRootPath."system/structure/blocks/schemablock.php");
		include_once($appRootPath."system/structure/module/modman.php");
		include_once($appRootPath."system/structure/areas/areaman.php");
		include_once($appRootPath."system/helpers/qstringmodifier.php");
		include_once($appRootPath."system/helpers/strsan.php");
		StrSan::init();
	}

	//	If we are needing to access surrounds
	if($flag&KS_SURROUND)
	{
		//	If we don't have modules
		if(!($flag&KS_MOD) && !($flag&KS_SURROUND_ONLY))
		{
			include_once($appRootPath."system/structure/blocks/templateholder.php");
			include_once($appRootPath."system/structure/blocks/assetholder.php");
		}
			
	}
	include_once($appRootPath."system/structure/areas/surroundman.php");

	if($flag&KS_ASSETS && !($flag&KS_MOD))
	
		include_once($appRootPath."system/structure/blocks/assetholder.php");


	if($flag&KS_PLUGIN)
	{
	}		
	//	Only required if app is loading a page
	if($flag&KS_IS_PAGE)
	{
		include_once($appRootPath."system/helpers/kxml.php");
		include_once($appRootPath."system/helpers/vpxml.php");
		include_once($appRootPath."system/layout/layman.php");
		include_once($appRootPath."system/structure/page.php");
		include_once($appRootPath."system/helpers/alinkgen.php");
	}

	if($flag&KS_DEBUG_MICRO)
	{
		include_once($appRootPath."system/debugmicro.php");
		if(isset($_GET['dbmsu']))
			Session::set('uid', $_GET['dbmsu']);
	}

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
		
		private function dbcConnect()
		{
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
			$result = $this->appDB->sendQuery($query);
			
			if(!$result)
				die("Major malfunction: root configuration error");

			$row = mysql_fetch_assoc($result);
			

			foreach($row as $key => $value)
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

			$this->appDB = Koda::getDatabaseConnection('mysql');

			if(!$this->dbcConnect()) {
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


			$this->layoutManager = new LayMan($this->appDB);
			Managers::setLayoutManager($this->layoutManager);

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
					$arc[$r[0]][] = $r[1];
			}

			$this->params['arc'] = $arc;

			if(!$this->runChannel('_on_page_request')) {
				KLog::error("_on_page_request channel failure");
				return false;
			}
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
			if($flag&KS_MOD && !($flag&KS_ASSETS)) {
				$this->areaManager = new AreaMan($this->appDB);
				Managers::setAreaManager($this->areaManager);
			}
			$this->resourceManager = new ResMan($this->appDB);
			Managers::setResourceManager($this->resourceManager);

			$this->channelManager = new ChanMan($this->appDB);
			Managers::setChannelManager($this->channelManager);

			$this->pluginManager = new PluginMan($this->appDB);
			Managers::setPluginManager($this->pluginManager);
		}

		private function runChannel($hook)
		{
			$chRid = $this->resourceManager->queryAssoc("Channel('$hook');");
			if(!$chRid)
				return false;

			$id = $this->resourceManager->getHandlerRef($chRid[0][0]);
			$channel = $this->channelManager->getChannel($id);
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
