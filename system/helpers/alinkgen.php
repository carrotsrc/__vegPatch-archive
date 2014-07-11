<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

	class ALinkGen
	{
		public static function generateLinkCSS($swtype, $name, $swid = null)
		{
			$urlReq = managers::appconfig()->setting('assetrequest');
			$urlReq = SystemConfig::appserverroot($urlReq);
			$link = "<link type=\"text/css\" rel=\"stylesheet\" href=\"http://$urlReq?loc=";
			$link .= "$swtype/";
			if($swtype != 0)
				$link .= "$swid/";
			$link .= "css/$name\" />\n";
			
			return $link;
		}

		public static function generateLinkJS($swtype, $name, $swid = null)
		{
			$urlReq = managers::appconfig()->setting('assetrequest');
			$urlReq = SystemConfig::appserverroot($urlReq);

			$link = "<script type=\"text/javascript\" src=\"http://$urlReq?";
			$link .= "loc=$swtype/";
			if($swtype != 0)
				$link .= "$swid/";

			$link .= "js/$name\"></script>\n";

			return $link;
		}

		public static function generateBatchLinkJS($area, $layout)
		{
			$urlReq = managers::appconfig()->setting('assetrequest');
			$urlReq = SystemConfig::appserverroot($urlReq);

			$link = "<script type=\"text/javascript\" src=\"http://$urlReq?";
			$link .= "loc=$area/$layout/js";

			$link .= "\"></script>\n";

			return $link;

		}

		public static function generateBatchLinkCSS($area, $layout)
		{
			$urlReq = managers::appconfig()->setting('assetrequest');
			$urlReq = SystemConfig::appserverroot($urlReq);
			$link = "<link type=\"text/css\" rel=\"stylesheet\" href=\"http://$urlReq?loc=";
			$link .= "$area/$layout/css";
			$link .= "\" />\n";
			
			return $link;

		}
	}
?>
