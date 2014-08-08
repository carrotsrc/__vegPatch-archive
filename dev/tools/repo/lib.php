<?php
	function render_collection_list($xml)
	{
		echo "<h2 style=\"margin-top:0px;\">Collections</h2>";
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
		echo "<h2 style=\"margin-top:0px;\">{$_GET['collection']}</h2>";
		echo "<a href=\"?tool=repo\">Back</a>";
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
	}

	function browse_collection($collection, $package, $url)
	{
		if($package) {
			return;
		}
		$url .= "$collection";
		$xml = file_get_contents($url);
		render_package_list($xml);
	}

	function browse_repo($url)
	{
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
