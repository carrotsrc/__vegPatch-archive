<?php

	date_default_timezone_set('UTC');

	class SystemConfig
	{	
		public static $KS_FLAG = 0;
		public static $masterSalt = null;

		public static $dbcUsername = '';
		public static $dbcPassword = '';
		public static $dbcDatabase = '';

		// added app dirctory property so you just change this to
		// set the application in a sub directory
		public static $appdir = ''; // '/app-dir'

		public static function libraryPath($sub = null)
		{
			if($sub == null)
				return $_SERVER["DOCUMENT_ROOT"].self::$appdir."/library/";
			else
				return $_SERVER["DOCUMENT_ROOT"].self::$appdir."/library/$sub";
		}
		
		public static function documentRootPath($sub = null)
		{
			if($sub == null)
				return $_SERVER["DOCUMENT_ROOT"];
			else
				return $_SERVER["DOCUMENT_ROOT"]."/$sub";
		}
		
		public static function appRootPath($sub = null)
		{
			if($sub == null)
				return $_SERVER["DOCUMENT_ROOT"].self::$appdir."/";
			else
				return $_SERVER["DOCUMENT_ROOT"].self::$appdir."/$sub";
		}
		public static function appServerRoot($sub = null)
		{
			if($sub == null)
				return $_SERVER["SERVER_NAME"].self::$appdir."/";
			else
				return $_SERVER["SERVER_NAME"].self::$appdir."/$sub";
		}

		public static function appRelativePath($sub = null)
		{
			if($sub == null)
				return self::$appdir."/";
			else
				return self::$appdir."/$sub";
		}

		public static function appScript()
		{
			return $_SERVER['SCRIPT_NAME'];
		}

		public static function relativeAppPath($sub)
		{
			$script = $_SERVER['PHP_SELF'];
			$atoms = explode('/', $script);
			$sz = sizeof($atoms)-2;
			$r = 0;
			$path = "";

			$chk = self::$appdir;
			if($chk == "");
				$chk = null;

			for($i = $sz; $i >= 0; $i--) {
				if($atoms[$i] == $chk)
					break;

				$r++;
			}
			
			for($i = $r; $i > 0; $i--)
				$path .= "../";

			$path .= $sub;
			
			return $path;
		}

		public static function relativeLibPath($sub)
		{
			return self::relativeAppPath("library".$sub);
		}
		public static function switchFlag($flag)
		{
			if(!(SystemConfig::$KS_FLAG&$flag))
				SystemConfig::$KS_FLAG |= $flag;
			else
				SystemConfig::$KS_FLAG -= $flag;
		}
		
		public static function checkFlag($flag)
		{
			if((SystemConfig::$KS_FLAG&$flag) == $flag)
				return true;
			else
				return false;
		}
	}
	
	
	// VegPatch import settings
	define('KS_NONE', 0);
	define('KS_MOD', 1);
	define('KS_SURROUND',2);
	define('KS_IS_PAGE', 4);
	define('KS_PLUGIN', 8);
	define('KS_USER', 16);
	define('KS_SURROUND_ONLY', 32);
	define('KS_SESSION', 64);
	define('KS_ASSETS', 128);
	define('KS_TRACK', 256);
	define('KS_PHP_ERROR', 512);
	define('KS_DEBUG_MICRO', 1024);

	// script check
	define('VP_LOADED', 255);

?>
