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

	}
?>
