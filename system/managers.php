<?php

/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	class Managers
	{
		private static $resourceManager = null;
		private static $appConfig = null;
		private static $channelManager = null;
		private static $pluginManager = null;
		private static $areaManager = null;
		private static $layoutManager = null;

		public static function setResourceManager($resman)
		{
			if(self::$resourceManager != null)
				return;

			self::$resourceManager = $resman;
		}

		public static function ResourceManager()
		{
			return self::$resourceManager;
		}

		public static function setAppConfig($config)
		{
			if(self::$appConfig != null)
				return;

			self::$appConfig = $config;
		}

		public static function AppConfig()
		{
			return self::$appConfig;
		}

		public static function setChannelManager($chanman)
		{
			if(self::$channelManager != null)
				return;

			self::$channelManager = $chanman;
		}

		public static function ChannelManager()
		{
			return self::$channelManager;
		}

		public static function setPluginManager($pluginman)
		{
			if(self::$pluginManager != null)
				return;

			self::$pluginManager = $pluginman;
		}

		public static function PluginManager()
		{
			return self::$pluginManager;
		}

		public static function setAreaManager($areaman)
		{
			if(self::$areaManager != null)
				return;

			self::$areaManager = $areaman;
		}

		public static function AreaManager()
		{
			return self::$areaManager;
		}

		public static function setLayoutManager($layman)
		{
			if(self::$layoutManager != null)
				return;
			self::$layoutManager = $layman;
		}

		public static function LayoutManager()
		{
			return self::$layoutManager;
		}
	}
?>
