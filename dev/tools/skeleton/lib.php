<?php
	function generateComponent($name, $res, $fm)
	{
		if(!$fm->makeDirectory(SystemConfig::relativeAppPath("library/components/$name")))
			return;

		$code = "";

		$code .= "<?php\n";
		$code .= "\tclass {$name}Component extends Component\n";
		$code .= "\t{\n";
		if($res) 
			$code .= "\t\tprivate \$resManager;\n\n";

		$code .= "\t\tpublic function initialize()\n";
		$code .= "\t\t{\n";
		if($res) 
			$code .= "\t\t\t\$this->resManager = Managers::ResourceManager();\n";
		$code .= "\t\t}\n";

		$code .= "\n";
		$code .= "\t\tpublic function run(\$channel = null, \$args = null)\n";
		$code .= "\t\t{\n";
			$code .= "\t\t\t\$response = null;\n\n";
			$code .= "\t\t\tswitch(\$channel) {\n";
				$code .= "\t\t\tcase 1:\n";
				$code .= "\t\t\t\t\n";
				$code .= "\t\t\tbreak;\n";
			$code .= "\t\t\t}\n\n";

			$code .= "\t\t\t if(\$args == null)\n";
			$code .= "\t\t\t\techo \$response;\n\n";
			$code .= "\t\t\treturn \$response;\n";
		$code .= "\t\t}\n";

		$code .= "\t}\n";
		$code .= "?>";
		$nfile = SystemConfig::relativeAppPath("library/components/$name/{$name}Component.php");
		$file = $fm->newFile($nfile, 'w');
		$file->write($code);
		$file->close();
	}

	function newComponentPanel()
	{
		echo "<b>Component Class</b>";
		echo "<form action=\"index.php?mode=component\" method=\"post\">";
		echo "<div class=\"form-item font-small\">";
			echo "<b>Component Name</b><br />";
			echo "<input type=\"text\" class=\"form-text\" style=\"margin-top: 0px\" name=\"label\" /><br />";
		echo "</div>";
		echo "<div class=\"form-item font-small\">";
			echo "Resource Manager";
			echo "<input type=\"checkbox\" class=\"form-text\" style=\"margin-top: 0px\" name=\"res\" /><br />";
			echo "<input type=\"hidden\" name=\"op\" value=\"1\" />";
			echo "<input type=\"submit\" class=\"form-button\" value=\"Create Class\" />";
		echo "</div>";
		echo "</form>";
	}

	function generatePanel($name, $space, $newspace, $fm)
	{
		$spaces = SystemConfig::relativeAppPath("library/panel/$space");
		if($newspace)
			$fm->makeDirectory($spaces);

		$pdir = $spaces."/$name";
		$fm->makeDirectory($pdir);
		$code = "";

		$code .= "<?php\n";
		$code .= "\tclass {$name}Panel extends Panel\n";
		$code .= "\t{\n";

		$code .= "\t\tpublic function __construct()\n";
		$code .= "\t\t{\n";
			$code .= "\t\t\tparent::__construct();\n";
		$code .= "\t\t}\n\n";

		$code .= "\t\tpublic function loadTemplate()\n";
		$code .= "\t\t{\n";
			$code .= "\t\t\t\n";
		$code .= "\t\t}\n\n";

		$code .= "\t\tpublic function initialize(\$params = null)\n";
		$code .= "\t\t{\n";
			$code .= "\t\t\tparent::initialize();\n";
		$code .= "\t\t}\n\n";

		$code .= "\n";
		$code .= "\t\tpublic function applyRequest(\$result)\n";
		$code .= "\t\t{\n";
			$code .= "\t\t\tforeach(\$result as \$rs) {\n\n";
				$code .= "\t\t\t\tswitch(\$rs['jack']) {\n";
					$code .= "\t\t\t\tcase 1:\n";
					$code .= "\t\t\t\t\t\n";
					$code .= "\t\t\t\tbreak;\n";
				$code .= "\t\t\t\t}\n";
			$code .= "\t\t\t}\n\n";

		$code .= "\t\t}\n\n";

		$code .= "\t\tpublic function setAssets()\n";
		$code .= "\t\t{\n";
			$code .= "\t\t\t\n";
		$code .= "\t\t}\n";

		$code .= "\t}\n";
		$code .= "?>";

		$nfile = $pdir."/{$name}Panel.php";
		$file = $fm->newFile($nfile, 'w');
		$file->write($code);
		$file->close();
	}

	function newPanelPanel($fm)
	{
		echo "<b>Panel Class</b>";
		echo "<form action=\"index.php?mode=panel\" method=\"post\">";
		echo "<div class=\"form-item font-small\">";
			echo "<b>Panel Name</b><br />";
			echo "<input type=\"text\" class=\"form-text\" style=\"margin-top: 0px\" name=\"label\" /><br />";
		echo "</div>";
		$spaces = $fm->listDirectories(SystemConfig::relativeAppPath("library/panel"));
		echo "<div class=\"form-item font-small\">";
		echo "<b>Space</b><br />";
		echo "<select name=\"space\" class=\"form-text form-select\" style=\"margin-top: 0px;\">";
			echo "<option value=\"000\">[New Space]</option>";
			foreach($spaces as $space)
				echo "<option value=\"$space\">$space</option>";

		echo "</select><br />";
		echo "<input type=\"text\" class=\"form-text font-small\" style=\"margin-top: 7px;\" name=\"nspace\" /><br />";

		echo "</div>";
		echo "<div class=\"form-item font-small\">";
			echo "<input type=\"hidden\" name=\"op\" value=\"1\" />";
			echo "<input type=\"submit\" class=\"form-button\" value=\"Create Class\" />";
		echo "</div>";
		echo "</form>";
	}

	function generateLibrary($name, $space, $newspace, $dbe, $fm)
	{
		$spaces = SystemConfig::relativeAppPath("library/lib/$space");
		if($newspace)
			$r = $fm->makeDirectory($spaces);


		$code = "";

		$code .= "<?php\n";
		$code .= "\tclass {$name}Library";
		if($dbe)
			$code .= " extends DBAcc";
		$code .= "\n";

		$code .= "\t{\n";

		if($dbe)
			$code .= "\t\tpublic function __construct(\$database)\n";
		else
			$code .= "\t\tpublic function __construct()\n";

		$code .= "\t\t{\n";

			if($dbe)
				$code .= "\t\t\t\$this->db = \$database;\n";
			else
				$code .= "\t\t\t\n";

		$code .= "\t\t}\n\n";

		$code .= "\t}\n";
		$code .= "?>";

		$nfile = $spaces."/{$name}Library.php";
		$file = $fm->newFile($nfile, 'w');
		$file->write($code);
		$file->close();
	}

	function newLibraryPanel($fm)
	{
		echo "<b>Library Class</b>";
		echo "<form action=\"index.php?mode=library\" method=\"post\">";
		echo "<div class=\"form-item font-small\">";
			echo "<b>Library Name</b><br />";
			echo "<input type=\"text\" class=\"form-text\" style=\"margin-top: 0px\" name=\"label\" /><br />";
		echo "</div>";
		$spaces = $fm->listDirectories(SystemConfig::relativeAppPath("library/lib"));
		echo "<div class=\"form-item font-small\">";
		echo "<b>Space</b><br />";
		echo "<select name=\"space\" class=\"form-text form-select\" style=\"margin-top: 0px;\">";
			echo "<option value=\"000\">[New Space]</option>";
			foreach($spaces as $space)
				echo "<option value=\"$space\">$space</option>";

		echo "</select><br />";
		echo "<input type=\"text\" class=\"form-text font-small\" style=\"margin-top: 7px;\" name=\"nspace\" /><br />";

		echo "</div>";
		echo "<div class=\"form-item font-small\">";
			echo "Database enabled";
			echo "<input type=\"checkbox\" class=\"form-text\" style=\"margin-top: 0px\" name=\"db\" /><br />";
			echo "<input type=\"hidden\" name=\"op\" value=\"1\" />";
			echo "<input type=\"submit\" class=\"form-button\" value=\"Create Class\" />";
		echo "</div>";
		echo "</form>";
	}

	function generatePlugin($name, $res, $fm)
	{
		if(!$fm->makeDirectory(SystemConfig::relativeAppPath("library/plugins/$name")))
			return;

		$code = "";

		$code .= "<?php\n";
		$code .= "\tclass {$name}Plugin extends Plugin\n";
		$code .= "\t{\n";
		if($res) 
			$code .= "\t\tprivate \$resManager;\n\n";

		$code .= "\t\tpublic function init(\$instance)\n";
		$code .= "\t\t{\n";
			$code .= "\t\t\tif(\$instance == null)\n";
			$code .= "\t\t\t\treturn false;\n\n";
			$code .= "\t\t\t\$this->setInstance(\$instance);\n";
		if($res) 
			$code .= "\n\t\t\t\$this->resManager = Managers::ResourceManager();\n";
		$code .= "\t\t}\n";

		$code .= "\n";
		$code .= "\t\tpublic function process(&\$params = null)\n";
		$code .= "\t\t{\n";
			$code .= "\t\t\t\n";
		$code .= "\t\t}\n";

		$code .= "\t}\n";
		$code .= "?>";
		$nfile = SystemConfig::relativeAppPath("library/plugins/$name/{$name}Plugin.php");
		$file = $fm->newFile($nfile, 'w');
		$file->write($code);
		$file->close();
	}
	function newPluginPanel()
	{
		echo "<b>Plugin Class</b>";
		echo "<form action=\"index.php?mode=plugin\" method=\"post\">";
		echo "<div class=\"form-item font-small\">";
			echo "<b>Plugin Name</b><br />";
			echo "<input type=\"text\" class=\"form-text\" style=\"margin-top: 0px\" name=\"label\" /><br />";
		echo "</div>";
		echo "<div class=\"form-item font-small\">";
			echo "Resource Manager";
			echo "<input type=\"checkbox\" class=\"form-text\" style=\"margin-top: 0px\" name=\"res\" /><br />";
			echo "<input type=\"hidden\" name=\"op\" value=\"1\" />";
			echo "<input type=\"submit\" class=\"form-button\" value=\"Create Class\" />";
		echo "</div>";
		echo "</form>";
	}
?>
