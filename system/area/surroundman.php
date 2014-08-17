<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	
	class SurroundMan
	{
		public static function includeTemplate($sId, $tId, $vars, $db)
		{
			//	Get surround path
			$result = $db->sendQuery("SELECT `surpool`.`name`, `surtemplate`.`value` FROM surpool JOIN `surtemplate` ON `surpool`.`id` = `surtemplate`.`s_id` WHERE `surpool`.`id`='$sId' AND `surtemplate`.`t_id`='$tId';");
			if(!$result)
				return null;

			$row = $result[0];
			$path = SystemConfig::appRootPath("library/surrounds/".$row['name']."/");
			$vars->media = SystemConfig::relativeAppPath("library/media/surrounds/{$row['name']}");
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
				
			$assetHolder = new AssetHolder();
			
			foreach($result as $row)
				$assetHolder->addAsset($row['type'], $row['value'], $row['name'], 3, $sId);

			return $assetHolder;
		}

		public static function getAssetPaths($sId, $db)
		{
			$query = "SELECT surpool.name, surasset.value, surasset.type FROM surasset ";
			$query .= "JOIN surpool ON surasset.s_id=surpool.id ";
			$query .= "WHERE surpool.id='$sId';";
			$result = $db->sendQuery($query);

			if(!$result)
				return null;

			$paths = array('js' => array(), 'css' => array());

			foreach($result as $row) {
				$path = "surrounds/{$row['name']}/{$row['value']}";
				$paths[$row['type']][] = $path;
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
