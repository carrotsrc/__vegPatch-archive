<?php
	function string_prepare_mysql($string)
	{
		if(get_magic_quotes_gpc())
			return $string;

		return str_replace(
			array('\\', '"','\''),
			array('\\\\', '\\"', '\\\'',),
			$string);
	}

	function string_prepare_xml($string)
	{
		return str_replace(
			array('&', '<','>','\'','"'),
			array('&amp;', '&lt;', '&gt;', '&apos;', '&quot;'),
			$string);
	}
	
	function string_clean_escapes($string)
	{
		if(!get_magic_quotes_gpc())
			return $string;

		return str_replace(
			array('\\\\', '\\"', '\\\'',),
			array('\\', '"','\''),
			$string);
	}

	function string_prepare_json($string)
	{
		return str_replace(
				array("\n", "/", "\b", "\f", "\r", "\t", "\u", "\\'", "\""),
				array("\\n","\\/", "\\b", "\\f", "\\r", "\\t", "\\u", "'", "\\\""),
				$string);
	}

	function json_encode_object($array, $alias = null)
	{
		echo "{";
		$sz = sizeof($array);
		foreach($array as $key => $item) {
			if(isset($alias[$key]))
				$key = $alias[$key];

			echo "\"$key\":";
			if(is_array($item) && is_numeric(key($item)))
				json_encode_array($item, $alias);
			else
			if(is_array($item))
				json_encode_object($item, $alias);
			else {
				if(is_numeric($item ))
					echo $item;
				else
					echo "\"".string_prepare_json($item)."\"";
			}
			if(--$sz > 0)
				echo ",";

		}
		echo "}";
	}

	function json_encode_array($array, $alias = null)
	{
		echo "[";
		$sz = sizeof($array);
		foreach($array as $key => $item) {
			if(isset($alias[$key]))
				$key = $alias[$key];

			if(is_array($item) && is_numeric(key($item)))
				json_encode_array($item, $alias);
			else
			if(is_array($item))
				json_encode_object($item, $alias);
			else {
				if(is_numeric($item ))
					echo $item;
				else
					echo "\"".string_prepare_json($item)."\"";
			}
			if(--$sz > 0)
				echo ",";

		}
		echo "]";
	}

?>
