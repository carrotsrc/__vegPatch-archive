<?php
	function render_collection_list($xml)
	{
		echo "<h2 style=\"margin-top:0px;\">Collections</h2>";
		echo "<div class=\"collection-list\">";
		$parser = new VPXML();
		$parser->init($xml);
		$record = "";
		while(($tag = $parser->getNextTag()) != null) {
			switch($tag->name) {
			case 'collection':
				while(($tag = $parser->getNextTag()) != null) {
					if($tag->name == "_text_") {
						echo "<a href=\"?tool=repo&collection={$tag->attributes['content']}\">{$tag->attributes['content']}</a><br/>";
					}
				}
			break;
			}
		}
		echo "</div>";

	}

	function render_package_list($xml)
	{
		$package = array();
		$trail = array();
		$i = 0;
		$state = 0;
		$j = 0;

		$parser = new VPXML();
		$parser->init($xml);
		while(($tag = $parser->getNextTag()) != null) {
			switch($tag->name) {
			case 'packages':
				$state = 1;
				break;

			case 'name':
				if($state != 1)
					break;
				$tag = $parser->getNextTag();
				if($tag->name != '_text_')
					break;

				$package[$i]['name'] = $tag->attributes['content'];
				break;

			case 'desc':
				if($state != 1)
					break;
				$tag = $parser->getNextTag();
				if($tag->name != '_text_')
					break;
				$package[$i]['desc'] = $tag->attributes['content'];
				break;

			case 'updated':
				if($state != 1)
					break;
				$tag = $parser->getNextTag();
				if($tag->name != '_text_')
					break;
				$package[$i]['updated'] = $tag->attributes['content'];
				break;

			case 'row':
				if($state == 1)
					$i++;
				else
				if($state == 2)
					$j++;
				break;
			case 'error':
				if($state != 1)
					break;
				$tag = $parser->getNextTag();
				if($tag->name != '_text_')
					break;
				echo $tag->attributes['content'];
				return;
				
			}
		}
		echo "<h2 style=\"margin-top:0px;\">";
		echo "<a href=\"?tool=repo\">Collections</a>";
		echo " / {$_GET['collection']}</h2>";
		echo "<div class=\"package-list\">";
		echo "<table>";
		foreach($package as $p) {
			echo "<tr>";
			echo "<td>";
				echo "<a href=\"?tool=repo&collection={$_GET['collection']}&package={$p['name']}\">{$p['name']}</a><br />";
			echo "</td>";
			echo "<td>";
				echo "{$p['desc']}<br />";
			echo "</td>";
			echo "<td>";
				echo "{$p['updated']}<br />";
			echo "</td>";
			echo "</tr>";
		}
		echo "</table>";
		echo "</div>";
	}

	function render_package_view($xml)
	{
		$package = array();
		$versions = array();
		$scm = array();
		$inc = array(0,0,0,0);
		$state = 0;



		$parser = new VPXML();
		$parser->init($xml);
		while(($tag = $parser->getNextTag()) != null) {
			switch($tag->name) {
			case 'package':
				$state = 1;
				break;

			case 'versions':
				$state = 2;
				break;

			case 'scm':
				$state = 3;
				break;

			case 'name':
			case 'desc':
				if($state != 1)
					break;
				$attr = $tag->name;
				$tag = $parser->getNextTag();
				if($tag->name != '_text_') {
					$package['desc'] = null;
					break;
				}
				$package[$attr] = $tag->attributes['content'];
				break;

			case 'updated':
				if($state != 1 && $state != 2)
					break;
				$tag = $parser->getNextTag();
				if($tag->name != '_text_')
					break;

				if($state == 1)
					$package['updated'] = $tag->attributes['content'];
				else
					$versions[$inc[$state]]['updated'] = $tag->attributes['content']; 

				break;

			case 'major':
			case 'minor':
			case 'maintenance':
			case 'stage':
			case 'deprecated':
			case 'archive':
			case 'created':
				if($state != 2)
					break;
				$attr = $tag->name;

				$tag = $parser->getNextTag();
				if($tag->name != '_text_')
					break;

				$versions[$inc[$state]][$attr] = $tag->attributes['content'];
				break;

			case 'url':
				if($state != 3)
					break;

				$attr = $tag->name;

				$tag = $parser->getNextTag();
				if($tag->name != '_text_')
					break;

				$scm[$inc[$state]][$attr] = $tag->attributes['content'];
				break;

				break;

			case 'row':
					$inc[$state]++;
				break;

			case 'error':
				$tag = $parser->getNextTag();
				if($tag->name != '_text_')
					return;

				echo $tag->attributes['content'];
				return;
			}
		}
		echo "<h2 style=\"margin-top:0px;\">";
		echo "<a href=\"?tool=repo\">Collections</a> / ";
		echo "<a href=\"?tool=repo&collection={$_GET['collection']}\">{$_GET['collection']}</a> / ";
		echo "{$_GET['package']}</h2>";
		echo "<h1>{$package['name']}</h1>";
		echo "<div class=\"package-desc\"><pre>";
		echo $package['desc'];

		echo "</pre></div>";
		echo "<table class=\"package-versions\">";
		foreach($versions as $k => $v) {
			if($v['deprecated'])
				continue;

			if($k == 1)
				echo "<tr class=\"top\">";
			else
				echo "<tr>";
			echo "<td>";
				echo "{$v['major']}.";
				echo "{$v['minor']}.";
				echo "{$v['maintenance']}";
				if(isset($v['stage']))
					echo "{$v['stage']}";
			echo "</td>";
			echo "<td>";
				if(isset($v['archive']))
					echo "<a href=\"?tool=unpack&repo={$GLOBALS['repo']}&collection={$_GET['collection']}&package={$_GET['package']}&archive={$v['archive']}\">{$v['archive']}</a>";
				else
					echo "&nbsp;";

			echo "</td>";
			echo "</tr>";
		}
		echo "</table>";
	}

	function browse_package($collection, $package, $url)
	{
		$url .= "$package";
		$xml = file_get_contents($url);
		render_package_view($xml);
	}

	function browse_collection($collection, $package, $url)
	{
		$url .= "$collection";
		if($package) {
			browse_package($collection, $package, $url);
			return;
		}
		$xml = file_get_contents($url);
		render_package_list($xml);
	}

	function browse_repo($url)
	{
		$GLOBALS['repo'] = $url;
		$url .= "/api/xml/collections/";
		$package = $collection = null;
		if(isset($_GET['collection']))
			$collection = $_GET['collection']."/";

		if(isset($_GET['package']))
			$package = $_GET['package']."/";

		if($collection) {
			browse_collection($collection, $package, $url);
			return;
		}
		$xml = file_get_contents($url);
		render_collection_list($xml);
	}
?>
