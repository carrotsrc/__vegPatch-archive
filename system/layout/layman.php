<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	include("wireframe.php");
	include("panelrep.php");

	class NContainer
	{
		public $type;
		public $attribute;
		public $content;
		public function generateHTML($index, $path)
		{
			echo "<div class=\"vpc-{$this->type}\" ";
			if(isset($this->attribute['style']))
				echo "style=\"{$this->attribute['style']}\"";
			echo ">\n";
			if(is_array($this->content)) {
				foreach($this->content as $c)
					$c->generateHTML($index, $path);
			} else {
				echo $this->content->generateHTML();
			}
			echo "\n</div>\n";
		}
	}

	class SContainer extends NContainer
	{
		public function generateHTML($index, $path)
		{
			foreach($this->content as $tag) {
				switch($tag->name) {
				case "_text_":
					echo $tag->attributes['content'];
					break;
				case "_comment_":
					break;

				default: 
					echo "<".$tag->name;
					if(($sz = sizeof($tag->attributes)) > 0) {
						$sz--;
						foreach($tag->attributes as $k => $v) {
							echo " $k=\"$v\"";
						}
					}
					echo ">";
					break;

				}
			}
		}

	}

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
				$cStack[$slen]->content[] = $tag;
				continue;
			}

			if($tag->name == "_text_") {
				continue;
			} else
			if($tag->name == "static") {
				$container = new SContainer();
				$container->type =  2;
				$container->content = array();
				$cStack[] = $container;
				$slen++;
			} else 
			if($tag->name[0] == "/")
			{
				$popped = $cStack[$slen];
				array_pop($cStack);
				$slen--;

				if($slen >= 0)
					$cStack[$slen]->content[] = $popped;
				else
					$cRoot[] = ($popped);
			} else {
				$pRep = null;
				if($tag->name == "leaf")
				{
					$container = new NContainer();
					$container->content = NULL;
					$pRep = new PanelRep();
					$pRep->setGroup(0);
				} else {
					$container = new NContainer();
					$container->content = array();
					if($container->type = $tag->name)
						foreach($tag->attributes as $p => $v)
							$container->attribute[$p] = $v;
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
							$container->attribute['style'] = $v;
						else
							$pRep->setStyle($v);
					break;

					case "vars":
						$pRep->setLoadVars($v);
					break;
					}
				}

				if($tag->name == 'leaf')
				{
					if($pRep != null) {
						$header[] = $pRep;
						$container->content = $pRep;
					}

					$cStack[$slen]->content[] = $container;
					$pRep = null;
				}
				else {
					$cStack[] = $container;
					$slen++;
				}
		
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
