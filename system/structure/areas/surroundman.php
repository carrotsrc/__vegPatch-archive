<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	
	class SurroundMan
	{
		public static function readTemplate($sId, $tId, $db)
		{
			//	Get surround path
			$query = "SELECT name FROM surpool WHERE id='$sId';";
			$result = $db->sendQuery($query);
			
			if(!$result)
				return null;
			
			if(mysql_num_rows($result) == 0)
				return null;
			
			$row = mysql_fetch_assoc($result);
			$path = SystemConfig::appRootPath("library/surrounds/".$row['name']."/");
			
			
			//	Get surround template
			$query = "SELECT value FROM surtemplate WHERE s_id='$sId' AND t_id='$tId';";
			$result = $db->sendQuery($query);
			
			if(!$result)
				return null;
			
			if(mysql_num_rows($result) == 0)
				return null;
		
		
			$row = mysql_fetch_assoc($result);
			$path .= $row['value'];
			
			$template = new TemplateHolder();
			$template->readTemplate($path);
			return $template;
		}
		
		public static function includeTemplate($sId, $tId, $vars, $db)
		{

			//	Get surround path
			$query = "SELECT name FROM surpool WHERE id='$sId';";
			$result = $db->sendQuery($query);
			
			if(!$result)
				return null;
			
			if(mysql_num_rows($result) == 0)
				return null;
			
			$row = mysql_fetch_assoc($result);
			$path = SystemConfig::appRootPath("library/surrounds/".$row['name']."/");
			
			/*
			*  TODO
			*  Include $vars as a class like the templates
			*  for panels
			*/
			//	Get surround template
			$query = "SELECT value FROM surtemplate WHERE s_id='$sId' AND t_id='$tId';";
			$result = $db->sendQuery($query);
			
			if(!$result)
				return null;
			
			if(mysql_num_rows($result) == 0)
				return null;
		
		
			$row = mysql_fetch_assoc($result);
			$path .= $row['value'];
			
			$template = new TemplateHolder();
			$template->includeTemplate($path, $vars);
			return $template;
		}
		
		public static function getSurroundAsset($sId, $aType, $aName, $db)
		{
			$query = "SELECT * FROM surasset WHERE s_id='$sId' AND name='$aName' AND type='$aType';";
			$result = $db->sendQuery($query);
			
			if(!$result)
				return null;
				
			if(mysql_num_rows($result) == 0)
				return null;
				
			$row = mysql_fetch_assoc($result);
			
			$path = $row['value'];
			if($path[0] == '/')
			{
				$path = SystemConfig::appRootPath("library/share".$path);
			}
			else
			{
				$query = "SELECT name FROM surpool WHERE id='$sId';";
				$result = $db->sendQuery($query);
				if(!$result)
					return null;
				
				if(mysql_num_rows($result) == 0)
					return null;
					
				$row = mysql_fetch_assoc($result);
				$path = "library/surrounds/".$row['name']."/".$path;
				$path = SystemConfig::appRootPath($path);
			}
			
			
			
			$fm = Koda::getFileManager();
			$asset = $fm->openFile($path, "r");
			$contents = $asset->read();
			return self::format($contents);
		}
		
		public static function getAssetList($sId, $db)
		{
			$query = "SELECT * FROM surasset WHERE s_id='$sId';";
			$result = $db->sendQuery($query);
			
			if(!$result)
				return null;
				
			if(mysql_num_rows($result) == 0)
				return null;
				
			$assetHolder = new AssetHolder();
			
			while(($row = mysql_fetch_assoc($result)) != false)
			{
				$assetHolder->addAsset($row['type'], $row['value'], $row['name'], 3, $sId);
			}
				
			return $assetHolder;
		}

		public static function getAssetPaths($sId, $db)
		{
			$query = "SELECT surpool.name, surasset.value, surasset.type FROM surasset ";
			$query .= "JOIN surpool ON surasset.s_id=surpool.id ";
			$query .= "WHERE surpool.id='$sId';";
			$result = $db->sendQuery($query, false, false);

			if(!$result)
				return null;

			$paths = array('js' => array(), 'css' => array());

			foreach($result as $row) {
				$path = "surrounds/{$row[0]}/{$row[1]}";
				$paths[$row[2]][] = $path;
			}

			return $paths;
		}

		private static function format($contents)
		{
			$contents = str_replace("__REQ_URL__", SystemConfig::appRootPath("system/req/ajaxport.php"), $contents);

			return $contents;
		}
	}
?>
