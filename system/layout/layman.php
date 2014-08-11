<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	include("container.php");
	include("nodecon.php");
	include("panelrep.php");
	include("leafcon.php");
	include("staticcon.php");
	include("wireframe.php");

	function core_process_wireframe($cml)
	{
		$header = array();
		$root = null;

		$parser = new VPXML();

		$parser->init($cml);
		$cContainer = null;
		$cStack = array();
		$cRoot = array();
		$slen = -1;
		while(($tag = $parser->getNextTag()) != null)
		{
			if($slen >= 0 && $cStack[$slen]->type == 2) {
				if($tag->name == "/static") {
					$popped = $cStack[$slen];
					array_pop($cStack);
					$slen--;
					if($slen >= 0)
						$cStack[$slen]->content[] = $popped;
					else
						$cRoot[] = ($popped);

					continue;
				}
				$cStack[$slen]->setContent($tag);
				continue;
			}

			if($tag->name == "_text_")
				continue;
			else
			if($tag->name == "node" || $tag->name == "leaf")
			{
				$pRep = null;
				if($tag->name == "node")
				{
					$container = new NodeCon();
					$container->content = array();
					foreach($tag->attributes as $p => $v)
						$container->addAttribute($p, $v);
				}
				else
				if($tag->name == "leaf")
				{
					$container = new LeafCon();
					$container->content = NULL;
					$pRep = new PanelRep();
					$pRep->setGroup(0);
				}

				foreach($tag->attributes as $p => $v)
				{
					switch($p) {
					case "type":
						$container->type = $v;
					break;
					
					case "pid":
						$pRep->setPanelId($v);
					break;

					case "cid":
						$pRep->setComponentId($v);
					break;

					case "ref":
						$pRep->setRef($v, true);
					break;

					case "rql":
						$pRep->setRef($v, false);
					break;

					case "grp":
						$pRep->setGroup($v);
					break;

					case "style":
						if($pRep == null)
							$container->addAttribute('style', $v);
						else
							$pRep->setStyle($v);
					break;

					case "vars":
						$pRep->setLoadVars($v);
					break;
					}
				}

				if($tag->name == "node") {
					$cStack[] = $container;
					$slen++;
				}
				else
				if($tag->name == "leaf")
				{
					if($pRep != null) {
						$header[] = $pRep;
						$container->content = $pRep;
					}

					$cStack[$slen]->content[] = $container;
					$pRep = null;
				}
		
			}
			else
			if($tag->name == "static") {
				$container = new StaticCon();
				$container->type =  2;
				$container->content = array();
				$cStack[] = $container;
				$slen++;
			}
			else
			if($tag->name == "/node")
			{
				$popped = $cStack[$slen];
				array_pop($cStack);
				$slen--;

				if($slen >= 0)
					$cStack[$slen]->content[] = $popped;
				else
					$cRoot[] = ($popped);
			}

		}
		return new Wireframe($header, $cRoot);

	}

	function core_load_layout($id, $db)
	{
		$result = $db->sendQuery("SELECT `name`, `cml` FROM `layoutpool` WHERE `id`='$id';");

		if(!$result)
			return null;

		if(!isset($result[0]['name']))
			return null;

		$wireframe = core_process_wireframe($result[0]['cml']);
		return $wireframe;
	}
?>
