<?php
/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	class StrSan
	{
		static public $magic = false;
		static private $mysqlFind;
		static private $mysqlReplace;

		static private $htmlFind;
		static private $htmlReplace;

		static private $sqhtmlFind;
		static private $sqhtmlReplace;
		public static function init()
		{
			self::$magic = get_magic_quotes_gpc();
			if(!self::$magic) {
				self::$mysqlFind = array( "\\");
				self::$mysqlReplace = array( "\\\\");
			}
			else {
				self::$mysqlFind = array();
				self::$mysqlReplace = array();
			}

			array_push(self::$mysqlFind, "\"", "'");
			array_push(self::$mysqlReplace, "\\\"", "\\'" );


			self::$htmlFind = array("<", ">", "\n", "\"");
			self::$htmlReplace = array("&#60;", "&#62;","<br />",  "&#34;");

			self::$sqhtmlFind = array("[b]", "[!b]", "[i]", "[!i]", "[u]", "[!u]");
			self::$sqhtmlReplace = array("<b>", "</b>", "<i>", "</i>", "<u>", "</u>");

		}

		public static function mysqlSanatize($str)
		{
			if(self::$magic)
				return $str;

			$lf = self::$mysqlFind;
			$lr = self::$mysqlReplace;
			$str = str_replace($lf, $lr, $str);
			return $str;
		}

		public static function mysqlDesanatize($str, $force = false)
		{
			/* TODO:
			*  this does not always behave consistantly
			*  find out why? Probably how sanatising is done
			*/
			if($str == "")
				return $str;

			if(self::$magic || $force)
				$str = stripslashes($str);

			return $str;
		}

		public static function htmlSanatize($str)
		{
			$lf = self::$htmlFind;
			$lr = self::$htmlReplace;

			$str = str_replace($lf, $lr, $str);
			return $str;
		}

		public static function sqhtmlSanatize($str)
		{
			//$pattern = "/\[(\/*\w+)\]/i";
			//$replace = "<$1>";
			//return preg_replace($pattern, $replace, $str);

			$lf = self::$sqhtmlFind;
			$lr = self::$sqhtmlReplace;

			$str = str_replace($lf, $lr, $str);
			return $str;
		}
	}
?>
