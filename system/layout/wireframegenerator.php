<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	include_once("container.php");
	include_once("nodecon.php");
	include_once("panelrep.php");
	include_once("leafcon.php");
	include_once("wireframe.php");

	class WireframeGenerator
	{
		public function processCML($cml)
		{
			$header = array();
			$root = null;

			$parser = new VPXML();

			$parser->init($cml);
			$cContainer = null;
			$cStack = array();
			$cRoot = array();
			while(($tag = $parser->getNextTag()) != null)
			{
				if($tag->element == "_text_")
					continue;

				$slen = sizeof($cStack)-1;
				if($tag->element == "node" || $tag->element == "leaf")
				{
					$pRep = null;
					if($tag->element == "node")
					{
						$container = new NodeCon();
						$container->content = array();
						foreach($tag->attributes as $p => $v)
							$container->addAttribute($p, $v);
					}
					else
					if($tag->element == "leaf")
					{
						$container = new LeafCon();
						$container->content = NULL;
						$pRep = new PanelRep();
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

					if($tag->element == "node")
						$cStack[] = $container;
					else
					if($tag->element == "leaf")
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
				if($tag->element == "/node")
				{
					$popped = $cStack[$slen];
					array_pop($cStack);
					$slen = sizeof($cStack);

					if($slen > 0)
						$cStack[$slen-1]->content[] = $popped;
					else
						$cRoot[] = ($popped);
				}

			}
			return new Wireframe($header, $cRoot);

		}

	}
?>
