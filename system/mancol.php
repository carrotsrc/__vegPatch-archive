<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	class Managers
	{
		private $resourceManager = null;
		private $moduleManager = null;
		public static function setResourceMananager($resman)
		{
			self::$resoureMananager = $resman;
		}

		public static function ResourceMananager()
		{
			return self::$resourceManager;
		}

		public static function setModuleManager($modman)
		{
			self::$moduleManager = $modman;
		}


		public static function ModuleManager()
		{
			return self::$moduleManager;
		}
	}
?>
